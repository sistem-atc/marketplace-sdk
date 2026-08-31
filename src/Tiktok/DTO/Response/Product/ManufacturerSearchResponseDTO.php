<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de busca de fabricantes. Pagina por token opaco.
 *
 * @property list<Manufacturer>|null $manufacturers
 */
final class ManufacturerSearchResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(Manufacturer::class)]
        public readonly ?array $manufacturers = null,
        public readonly ?int $totalCount = null,
        public readonly ?string $nextPageToken = null,
    ) {}
}
