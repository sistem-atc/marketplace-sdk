<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de `POST /fbt/202607/goods/search`.
 *
 * Paginacao por page token opaco (`nextPageToken`), igual ao search de pedidos.
 * Filtros vazios = devolve a pagina mais recente por data de criacao; filtros
 * combinados sao INTERSECAO (nao uniao) — passar goods_ids + sku_ids devolve so'
 * o que satisfaz os dois.
 *
 * @property list<FbtGoods>|null $goods
 */
final class FbtGoodsSearchResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $nextPageToken = null,
        public readonly ?int $totalCount = null,
        #[ArrayOf(FbtGoods::class)]
        public readonly ?array $goods = null,
    ) {}
}
