<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\LojaIntegrada\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\LojaIntegrada\DTO\Response\Order\FormaPagamentoConfig;

/** Forma de pagamento (pagamentos[].forma_pagamento). */
final class FormaPagamento implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $codigo = null,
        public readonly ?string $nome = null,
        public readonly ?string $imagem = null,
        public readonly ?string $resourceUri = null,
        public readonly ?FormaPagamentoConfig $configuracoes = null,
    ) {}
}
