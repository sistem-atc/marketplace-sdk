<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Preco do SKU global (`skus[].price`).
 *
 * ATENCAO: shape DIFERENTE do preco do produto local (que usa
 * `tax_exclusive_price`/`sale_price`). Aqui e' o preco UNIFORME GLOBAL
 * pre-imposto (`amount`), em USD (global) ou EUR (intra-UE); ao publicar,
 * o TikTok converte pro preco local por cambio + taxas do mercado.
 *
 * Dinheiro e' STRING sempre. `unitPrice` so' existe no mercado UE e depende
 * de `sku_unit_count`/base unit count terem sido definidos na criacao.
 */
final class GlobalSkuPrice implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $amount = null,
        public readonly ?string $currency = null,
        public readonly ?string $unitPrice = null,
    ) {}
}
