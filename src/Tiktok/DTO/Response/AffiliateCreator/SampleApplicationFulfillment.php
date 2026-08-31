<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Contrapartida de conteudo de uma amostra — item de `fulfillments[]`.
 *
 * Diferente do CreatorFulfillment aninhado na application, aqui vem
 * `shopId`/`productId`/`sampleApplicationType` achatados: da' pra ligar a
 * amostra ao produto sem uma segunda chamada.
 */
final class SampleApplicationFulfillment implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $shopId = null,
        public readonly ?string $applicationId = null,
        // FREE_SAMPLE | SAMPLE_COUPON | SAMPLE_CAMPAIGN | PLATFORM_FREE_SAMPLE
        public readonly ?string $sampleApplicationType = null,
        public readonly ?string $productId = null,
        public readonly ?int $expirationTime = null,
        public readonly ?int $totalSuspendDuration = null,
        public readonly ?string $status = null,
        public readonly ?string $boundProductStatus = null,
    ) {}
}
