<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\User;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** `phone` / `alternative_phone`. */
final class UserPhone implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $areaCode = null,
        public readonly ?string $number = null,
        public readonly ?string $extension = null,
        public readonly ?bool $verified = null,
    ) {}
}
