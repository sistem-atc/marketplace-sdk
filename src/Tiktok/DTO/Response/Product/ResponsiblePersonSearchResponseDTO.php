<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de busca de pessoas responsaveis. Pagina por token opaco.
 *
 * @property list<ResponsiblePerson>|null $responsiblePersons
 */
final class ResponsiblePersonSearchResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(ResponsiblePerson::class)]
        public readonly ?array $responsiblePersons = null,
        public readonly ?int $totalCount = null,
        public readonly ?string $nextPageToken = null,
    ) {}
}
