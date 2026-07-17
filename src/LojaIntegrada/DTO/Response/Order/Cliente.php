<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\LojaIntegrada\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;


/** Comprador (cliente). PII sem máscara; cpf OU cnpj. */
final class Cliente implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $nome = null,
        public readonly ?string $email = null,
        public readonly ?string $cpf = null,
        public readonly ?string $cnpj = null,
        public readonly ?string $razaoSocial = null,
        public readonly ?string $telefoneCelular = null,
        public readonly ?string $telefonePrincipal = null,
        public readonly ?string $dataNascimento = null,
        public readonly ?string $sexo = null,
        public readonly ?string $resourceUri = null,
    ) {}
}
