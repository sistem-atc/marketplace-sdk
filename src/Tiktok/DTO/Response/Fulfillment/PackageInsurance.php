<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Seguro contratado automaticamente na compra da etiqueta.
 *
 * `coverageAmount` e' STRING e a doc fixa a unidade em USD — NAO e' a moeda
 * do pedido. Converter antes de somar com valor de venda em BRL.
 */
final class PackageInsurance implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?bool $isPurchased = null,
        public readonly ?string $coverageAmount = null,
        public readonly ?bool $isClaimEligible = null,
        /** NOT_STARTED | CLAIM_PENDING | APPROVED | DECLINED */
        public readonly ?string $claimStatus = null,
    ) {}
}
