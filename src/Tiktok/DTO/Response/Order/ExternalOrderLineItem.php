<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Amarracao de UM item: nosso id de linha <-> id de linha do TikTok.
 *
 * ARMADILHA DE NOMES — os dois campos sao invertidos em relacao ao que a
 * intuicao sugere:
 *   - `id`        = id da linha NO NOSSO sistema (o que gravamos la);
 *   - `originId`  = id da linha NO TIKTOK (a "origem" do pedido).
 *
 * Trocar os dois grava a referencia espelhada e a busca por referencia externa
 * devolve vazio sem nenhum erro.
 */
final class ExternalOrderLineItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Id da linha no NOSSO sistema. */
        public readonly ?string $id = null,
        /** Id da linha no TIKTOK (`line_items[].id` do pedido). */
        public readonly ?string $originId = null,
    ) {}
}
