<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Reverse;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` do Search Aftersales Request (`/return_refund/202603/aftersales/search`).
 *
 * Paginacao por TOKEN OPACO, nao por offset: `nextPageToken` vazio/ausente
 * significa fim. `totalCount` e' o tamanho do resultado INTEIRO, independente
 * da pagina — nao use pra saber se acabou.
 *
 * @property list<AftersalesRequest>|null $aftersalesRequests
 */
final class SearchAftersalesResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(AftersalesRequest::class)]
        public readonly ?array $aftersalesRequests = null,
        public readonly ?int $totalCount = null,
        public readonly ?string $nextPageToken = null,
    ) {}
}
