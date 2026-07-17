<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\LojaIntegrada\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;


/** Situação do pedido (situacao). aprovado/cancelado/final gateiam o pipeline. */
final class Situacao implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $codigo = null,
        public readonly ?string $nome = null,
        public readonly ?bool $aprovado = null,
        public readonly ?bool $cancelado = null,
        public readonly ?bool $final = null,
        public readonly ?bool $padrao = null,
        public readonly ?bool $notificarComprador = null,
        public readonly ?string $resourceUri = null,
    ) {}
}
