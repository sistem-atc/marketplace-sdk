<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de `POST /product/202601/compliance/auditing/research`.
 *
 * Busca produtos na base do TikTok por titulo/marca/categoria — inclusive de
 * outros vendedores (`seller_id`/`seller_name` sao filtros). Nao devolve preco
 * nem estoque; serve pra compliance e comparacao de anuncio, nao pra monitor
 * de preco. `nextPageToken` vem como STRING contendo um JSON (`["1733..."]`).
 */
final class ProductAuditingResearchResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $nextPageToken = null,
        public readonly ?int $totalCount = null,
        #[ArrayOf(AuditingProduct::class)]
        public readonly ?array $products = null,
    ) {}
}
