<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\LojaIntegrada\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Wrapper do endpoint `pedido/search/` (Tastypie): meta + objects[].
 *
 * ATENÇÃO: a LISTAGEM Tastypie é FINA (`full=False`) — em `objects[]` as
 * relações (cliente, situacao, endereco...) vêm como STRING resource_uri
 * (`/api/v1/cliente/123`), não como o objeto aninhado do detalhe. Por isso
 * `objects` é PASSTHROUGH cru (não tipa em OrderResponseDTO, que modela o
 * DETALHE de `pedido/{numero}/`): hidratar a shape fina no DTO cheio estoura.
 * O único consumidor lê apenas `meta.total_count`. Ver
 * [[marketplace-sdk-dto-pattern]].
 */
final class OrderSearchResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param array<int,array<string,mixed>> $objects */
    public function __construct(
        public readonly ?SearchMeta $meta = null,
        public readonly array $objects = [],
    ) {}
}
