<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\User;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** `identification` — documento do usuário (`{type, number}`). */
final class UserIdentification implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $type = null,
        public readonly ?string $number = null,
    ) {}
}
