<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de `POST /product/202604/opportunities/query`.
 *
 * QUIRK: a API embrulha em `data.data` (duas camadas) — o metodo do SDK
 * desembrulha as duas antes de hidratar.
 */
final class OpportunityListResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(OpportunitySummary::class)]
        public readonly ?array $opportunities = null,
        public readonly ?string $nextPageToken = null,
        public readonly ?int $totalCount = null,
    ) {}
}
