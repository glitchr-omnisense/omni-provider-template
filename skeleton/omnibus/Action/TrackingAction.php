<?php

namespace __Namespace__\Action;

use __Namespace__\Api;
use Omnibus\Action\ActionInterface;
use Omnibus\Action\ApiAwareInterface;
use Omnibus\Action\ApiAwareTrait;
use Omnibus\Exception\CarrierException;
use Omnibus\Model\Tracking as TrackingModel;
use Omnibus\Model\TrackingEvent;
use Omnibus\Model\TrackingStatus;
use Omnibus\Request\Request;
use Omnibus\Request\Tracking;

/**
 * Where a parcel is: GET tracking/{number}, read as
 *
 *   {"tracking_number": "...", "status": "in_transit",
 *    "events": [{"at": "2026-10-20T08:12:00+02:00", "status": "pending", "description": "...", "location": "...", "code": "..."}]}
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

    public function __construct(private readonly string $carrier)
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
        $data = $this->api->request('GET', 'tracking/'.rawurlencode($request->trackingNumber));

        $events = [];
        foreach (\is_array($data['events'] ?? null) ? $data['events'] : [] as $event) {
            if (!\is_array($event)) {
                continue;
            }
            $events[] = new TrackingEvent(
                $this->date($event['at'] ?? null),
                self::status($event['status'] ?? null),
                self::string($event['description'] ?? null) ?? '',
                self::string($event['location'] ?? null),
                self::string($event['code'] ?? null),
            );
        }
        usort($events, static fn (TrackingEvent $a, TrackingEvent $b) => $a->at <=> $b->at);

        $request->setResult(new TrackingModel($this->carrier, $request->trackingNumber, self::status($data['status'] ?? null), $events));
    }

    private function date(mixed $value): \DateTimeImmutable
    {
        try {
            return new \DateTimeImmutable(self::string($value) ?? throw new \InvalidArgumentException('no date'));
        } catch (\Exception $e) {
            throw new CarrierException($this->carrier, 'A tracking event without a readable date.', null, $e);
        }
    }

    private static function status(mixed $code): TrackingStatus
    {
        return self::STATUSES[self::string($code) ?? ''] ?? TrackingStatus::UNKNOWN;
    }

    private static function string(mixed $value): ?string
    {
        return \is_scalar($value) && '' !== (string) $value ? (string) $value : null;
    }
}
