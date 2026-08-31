<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de exclusao em lote de produtos globais.
 *
 * ARMADILHA: sucesso parcial. A chamada volta code 0 e `errors` traz SO' os
 * que falharam — lista vazia/ausente e' que significa "todos apagados".
 *
 * @property list<GlobalDeleteError>|null $errors
 */
final class GlobalProductDeleteResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(GlobalDeleteError::class)]
        public readonly ?array $errors = null,
    ) {}
}
