<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Faixa de CONTAGEM (unidades vendidas, taxa de comissao) — aqui min/max sao
 * INT, nao string, porque nao e' dinheiro. Taxa de comissao vem em centesimos
 * de por cento: 6000 = 60,00%.
 */
final class AffiliateCountRange implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $minimumAmount = null,
        public readonly ?int $maximumAmount = null,
        public readonly ?string $formattedRange = null,
    ) {}
}
