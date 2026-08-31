<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Caixa PLANEJADA de um inbound.
 *
 * `quantity` aqui e' o numero de CAIXAS IDENTICAS, nao de pecas — a quantidade
 * de pecas esta' em `items[].quantity`. Total enviado = quantity x soma dos
 * items. Confundir os dois erra o volume da nota de remessa por um fator
 * inteiro.
 *
 * @property list<FbtCartonItem>|null $items
 */
final class FbtPlannedCarton implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        // SINGLE_SKU | MULTI_SKU
        public readonly ?string $cartonType = null,
        #[ArrayOf(FbtCartonItem::class)]
        public readonly ?array $items = null,
        public readonly ?int $quantity = null,
        /** @var list<string>|null etiquetas das caixas ("C0001", "C0002") */
        public readonly ?array $cartonNums = null,
        public readonly ?FbtBoxMeasurements $boxMeasurements = null,
    ) {}
}
