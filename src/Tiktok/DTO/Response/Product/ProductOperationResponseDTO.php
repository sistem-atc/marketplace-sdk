<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de activate / deactivate / recover de produtos (mesma shape nos tres).
 *
 * ARMADILHA: e' uma operacao em LOTE (max 20 IDs) e o sucesso vem VAZIO — a
 * resposta so' lista o que FALHOU. `errors` vazio = todos os IDs passaram.
 */
final class ProductOperationResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(ProductOperationError::class)]
        public readonly ?array $errors = null,
    ) {}
}
