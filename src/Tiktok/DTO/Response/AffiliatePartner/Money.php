<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliatePartner;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Valor monetário do Affiliate Partner.
 *
 * `amount` é STRING — e vem em formas inconsistentes no mesmo payload ("99",
 * "3.00", "0"). Tipar float apagaria a forma original e arredondaria centavo.
 */
final class Money implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $amount = null,
        public readonly ?string $currency = null,
    ) {}
}
