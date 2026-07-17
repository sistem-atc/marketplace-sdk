<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\User;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** `address` do usuário (endereço curto do cadastro). */
final class UserAddress implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $address = null,
        public readonly ?string $city = null,
        public readonly ?string $state = null,
        public readonly ?string $zipCode = null,
    ) {}
}
