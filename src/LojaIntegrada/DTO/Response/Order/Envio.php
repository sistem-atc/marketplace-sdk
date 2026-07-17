<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\LojaIntegrada\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\LojaIntegrada\DTO\Response\Order\FormaEnvio;

/** Envio/entrega (envios[]). */
final class Envio implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $valor = null,
        public readonly ?int $prazo = null,
        public readonly ?string $objeto = null,
        public readonly ?string $pedido = null,
        public readonly ?FormaEnvio $formaEnvio = null,
        public readonly ?string $dataCriacao = null,
        public readonly ?string $dataModificacao = null,
    ) {}
}
