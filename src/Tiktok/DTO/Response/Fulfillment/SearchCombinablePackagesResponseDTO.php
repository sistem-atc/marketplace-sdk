<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resultado de GET /fulfillment/202309/combinable_packages/search.
 *
 * ATENCAO: os `id` aqui sao pacotes PRE-GERADOS (rascunho) — so' passam a
 * existir de fato quando o combine e' confirmado. Nao guarde como package_id
 * definitivo.
 *
 * @property list<CombinedPackage>|null $combinablePackages
 */
final class SearchCombinablePackagesResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(CombinedPackage::class)]
        public readonly ?array $combinablePackages = null,
        public readonly ?string $nextPageToken = null,
        public readonly ?int $totalCount = null,
    ) {}
}
