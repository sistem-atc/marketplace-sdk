<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\LojaIntegrada\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;


/** pagamentos[].forma_pagamento.configuracoes. */
final class FormaPagamentoConfig implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?bool $ativo = null,
        public readonly ?bool $disponivel = null,
    ) {}
}
