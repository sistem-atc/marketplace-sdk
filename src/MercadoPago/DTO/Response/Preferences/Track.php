<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Preferences;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Preference Track resource. Represents a tracking pixel or conversion tag configuration for a
 * checkout preference. Supports Google Ads ("google_ad") and Facebook Pixel ("facebook_ad")
 * tracking types, enabling conversion measurement when buyers complete the checkout flow.
 */
final class Track implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Track type (google_ad or facebook_ad). */
        public readonly ?string $type = null,

        /** Values according the track type. */
        public readonly ?TrackValues $values = null,
    ) {}
}
