<?php

namespace __Namespace__\Action;

use __Namespace__\Api;
use __Namespace__\Data;
use Omnibus\Action\ActionInterface;
use Omnibus\Action\ApiAwareInterface;
use Omnibus\Action\ApiAwareTrait;
use Omnibus\Model\Address;
use Omnibus\Model\PickupPoint;
use Omnibus\Request\Pickup;
use Omnibus\Request\Request;
use Omnisense\Contracts\Policy\PolicyGuard;

/**
 * The pickup points near an address, nearest first: GET pickup-points, page
 * by page, read as
 *
 *   {"points": [{"id", "name", "street", "postcode", "city", "country", "latitude", "longitude", "distance"}], "next_page": 2}
 *
 * until the caller's limit is reached or "next_page" is null. The registry's
 * guard counts the operation ("pickupPoints") once; each page after the first
 * is a request it does not see: they go through the guard the factory gives
 * (Config::guard(), PolicyGuard::pages()), under the same operation - each
 * checked before it is sent and counted after, the last answer never counted
 * as one more request. Replace the path, the fields and the paging with the
 * service's (a cursor, an offset: $next reads it from the page).
 */
final class PickupPointsAction implements ActionInterface, ApiAwareInterface
{
    /** @use ApiAwareTrait<Api> */
    use ApiAwareTrait;

    public function __construct(private readonly string $carrier, private readonly PolicyGuard $pages)
    {
        $this->apiClass = Api::class;
    }

    public function supports(Request $request): bool
    {
        return $request instanceof Pickup;
    }

    public function execute(Request $request): void
    {
        \assert($request instanceof Pickup);
        $page = fn (int $number): Data => $this->api->request('GET', 'pickup-points?'.http_build_query([
            'page' => $number,
            'postcode' => $request->near->postcode,
            'country' => $request->near->country,
        ]));

        $points = [];
        $next = static function (Data $read) use (&$points, $page, $request): ?\Closure {
            $number = $read->int('next_page');

            return null === $number || \count($points) >= $request->limit ? null : static fn (): Data => $page($number);
        };
        foreach ($this->pages->pages('pickupPoints', static fn (): Data => $page(1), $next) as $read) {
            foreach ($read->list('points') as $point) {
                if (\count($points) < $request->limit) {
                    $points[] = $this->point($point);
                }
            }
        }

        $request->setResult($points);
    }

    private function point(Data $point): PickupPoint
    {
        $street = $point->string('street');

        return new PickupPoint(
            carrier: $this->carrier,
            id: $point->text('id'),
            name: $point->text('name'),
            address: new Address($point->text('name'), null === $street ? [] : [$street], $point->text('postcode'), $point->text('city'), strtoupper($point->text('country'))),
            latitude: $point->float('latitude'),
            longitude: $point->float('longitude'),
            distance: $point->int('distance'),
        );
    }
}
