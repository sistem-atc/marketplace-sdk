<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de `GET /product/202312/prerequisites`.
 *
 * Roda ANTES de tentar publicar: se a loja nao tem armazem de devolucao,
 * template de frete etc, todo Create Product vai falhar. `isFailed` = true e'
 * o bloqueio (a semantica e' invertida, cuidado).
 */
final class ListingPrerequisitesResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(PrerequisiteCheckResult::class)]
        public readonly ?array $checkResults = null,
    ) {}
}
