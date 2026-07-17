<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\LojaIntegrada\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;


/** Endereço de entrega (endereco_entrega). */
final class Endereco implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $nome = null,
        public readonly ?string $endereco = null,
        public readonly ?string $numero = null,
        public readonly ?string $complemento = null,
        public readonly ?string $bairro = null,
        public readonly ?string $cidade = null,
        public readonly ?string $estado = null,
        public readonly ?string $cep = null,
        public readonly ?string $pais = null,
        public readonly ?string $cpf = null,
        public readonly ?string $cnpj = null,
        public readonly ?string $ie = null,
        public readonly ?string $rg = null,
        public readonly ?string $razaoSocial = null,
        public readonly ?string $referencia = null,
        public readonly ?string $tipo = null,
    ) {}
}
