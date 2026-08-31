<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliatePartner;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Status de entrega da amostra que o vendedor mandou pro criador. */
final class SampleStatus implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $shippingProviderName = null,
        /** ECONOMY_SHIPPING | PREMIUM_SHIPPING */
        public readonly ?string $deliveryOption = null,
        /** STRING com epoch, apesar do nome "date". */
        public readonly ?string $estimatedEarliestDeliveryDate = null,
        public readonly ?string $estimatedLatestDeliveryDate = null,
        public readonly ?int $quantity = null,
        #[ArrayOf(SampleTrackingResult::class)]
        public readonly ?array $trackingResults = null,
    ) {}
}
