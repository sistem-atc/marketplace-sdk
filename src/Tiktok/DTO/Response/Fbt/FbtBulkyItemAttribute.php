<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Marcacao de item volumoso — muda a faixa de tarifa de armazenagem/manuseio. */
final class FbtBulkyItemAttribute implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?bool $isBulkyItem = null,
        public readonly ?bool $isBulkyItemAccessories = null,
    ) {}
}
