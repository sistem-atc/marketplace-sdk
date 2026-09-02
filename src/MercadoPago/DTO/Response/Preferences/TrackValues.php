<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Preferences;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Preference Track Values resource. Contains the platform-specific identifiers for conversion
 * tracking. For Google Ads, provides conversion_id and conversion_label (used with GTM). For
 * Facebook, provides the pixel_id for the Facebook Pixel.
 */
final class TrackValues implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** conversion_id for GTM Google Ads Conversion Tracking tag. */
        public readonly ?string $conversionId = null,

        /** conversion_label for GTM Google Ads Conversion Tracking tag. */
        public readonly ?string $conversionLabel = null,

        /** pixel_id for Facebook Pixel. */
        public readonly ?string $pixelId = null,
    ) {}
}
