<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Customizacao (POD — print on demand) de UMA linha do pedido.
 *
 * `podDetail` vem como JSON SERIALIZADO dentro de uma string, nao como objeto:
 * o schema do conteudo varia por produto customizavel, entao o TikTok nao o
 * declara. Guarde a string crua e faca `json_decode` sob demanda — a doc nao
 * garante nem as chaves nem os tipos de dentro.
 *
 * A doc oficial troca as descricoes destes dois campos (diz que `pod_detail`
 * e' "o id da linha" e que `order_line_id` e' "o JSON"); os nomes e o exemplo
 * mostram o contrario, e e' o contrario que vale.
 */
final class PodDetail implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $orderLineId = null,
        /** JSON serializado em string; schema variavel por produto. */
        public readonly ?string $podDetail = null,
    ) {}
}
