<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Item;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Bloco `shipping` do anúncio — modalidade/frete grátis/logistic_type.
 *
 * @property array<int|string, mixed> $methods
 * @property array<int|string, mixed> $tags
 */
final class ItemShipping implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /**
     * @param  array<int|string, mixed>  $methods
     * @param  array<int|string, mixed>  $tags
     */
    public function __construct(
        public readonly ?string $mode = null,
        public readonly ?bool $freeShipping = null,
        public readonly ?bool $localPickUp = null,
        public readonly ?bool $storePickUp = null,
        public readonly ?string $logisticType = null,
        public readonly mixed $dimensions = null,
        public readonly array $methods = [],
        public readonly array $tags = [],
    ) {}
}
