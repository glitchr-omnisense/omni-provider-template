<?php

namespace __Namespace__\Action;

use __Namespace__\Api;
use __Namespace__\Data;
use Omnibus\Action\ActionInterface;
use Omnibus\Action\ApiAwareInterface;
use Omnibus\Action\ApiAwareTrait;
use Omnibus\Exception\CarrierException;
use Omnibus\Model\Tracking as TrackingModel;
use Omnibus\Model\TrackingEvent;
use Omnibus\Model\TrackingStatus;
use Omnibus\Request\Request;
use Omnibus\Request\Tracking;
use Omnisense\Contracts\Policy\PolicyGuard;

/**
 * Where a parcel is: GET tracking/{number}, read as
 *
 *   {"tracking_number": "...", "status": "in_transit",
 *    "events": [{"at": "2026-10-20T08:12:00+02:00", "status": "pending", "description": "...", "location": "...", "code": "..."}]}
 *
 * A carrier may limit how often one parcel is tracked (Mondial Relay: 6 times
 * a day). policy.json then declares it per resource -
 * {"max": 6, "window": "day", "scope": "resource", "per": "parcel",
 * "operations": ["trackParcel"]} - and each request goes through the guard
 * the factory gives (PolicyGuard::perResource(): the per-resource limits
 * alone, so that no other limit counts the request twice), under
 * "trackParcel", the tracking number named: over the limit, the request is
 * refused unsent (UsageLimitReachedException). Without such a limit, the
 * guard counts nothing.
 *
 * Replace the path, the fields and STATUSES (the carrier's codes on the
 * family's scale) with the service's.
 */
final class TrackingAction implements ActionInterface, ApiAwareInterface
{
    /** @use ApiAwareTrait<Api> */
    use ApiAwareTrait;

    /** The carrier's statuses, in the family's words; any other is UNKNOWN. */
    private const STATUSES = [
        'pending' => TrackingStatus::PENDING,
        'in_transit' => TrackingStatus::IN_TRANSIT,
        'out_for_delivery' => TrackingStatus::OUT_FOR_DELIVERY,
        'ready_for_pickup' => TrackingStatus::AVAILABLE_FOR_PICKUP,
        'delivered' => TrackingStatus::DELIVERED,
        'exception' => TrackingStatus::EXCEPTION,
        'returned' => TrackingStatus::RETURNED,
    ];

    /** @param ?PolicyGuard $parcels the policy's limits per parcel (PolicyGuard::perResource()); null: none counted here */
    public function __construct(private readonly string $carrier, private readonly ?PolicyGuard $parcels = null)
    {
        $this->apiClass = Api::class;
    }

    public function supports(Request $request): bool
    {
        return $request instanceof Tracking;
    }

    public function execute(Request $request): void
    {
        \assert($request instanceof Tracking);
        $track = fn (): Data => $this->api->request('GET', 'tracking/'.rawurlencode($request->trackingNumber));
        $data = null === $this->parcels ? $track() : $this->parcels->call('trackParcel', $track, ['resource' => $request->trackingNumber]);

        $events = [];
        foreach ($data->list('events') as $event) {
            $events[] = new TrackingEvent(
                $event->dateTime('at') ?? throw new CarrierException($this->carrier, 'A tracking event without a readable date.'),
                self::status($event->string('status')),
                $event->string('description') ?? '',
                $event->string('location'),
                $event->string('code'),
            );
        }
        usort($events, static fn (TrackingEvent $a, TrackingEvent $b) => $a->at <=> $b->at);

        $request->setResult(new TrackingModel($this->carrier, $request->trackingNumber, self::status($data->string('status')), $events));
    }

    private static function status(?string $code): TrackingStatus
    {
        return self::STATUSES[$code ?? ''] ?? TrackingStatus::UNKNOWN;
    }
}
