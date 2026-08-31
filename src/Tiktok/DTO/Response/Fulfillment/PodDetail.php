<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Customizacao POD (print-on-demand) de um item do pedido.
 *
 * `podInfoJson` e' JSON **dentro de uma string** — a API nao expoe a
 * estrutura; faca json_decode por sua conta e trate schema livre.
 */
final class PodDetail implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $podInfoJson = null,
        public readonly ?string $orderLineId = null,
    ) {}
}
