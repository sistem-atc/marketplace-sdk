<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Orders;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Represents a tracking pixel fired at checkout completion. Supports Google Ads and Facebook Ads
 * tracking to attribute conversions from the Checkout PRO flow. Configured within OnlineConfig.
 */
final class Track implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Tracking pixel type. Accepted values: "google_ad" or "facebook_ad". */
        public readonly ?string $type = null,

        /** Key-value pairs specific to the tracking type. For "google_ad": conversion_id and conversion_label. For "facebook_ad": pixel_id. */
        public readonly ?array $values = null,
    ) {}
}
