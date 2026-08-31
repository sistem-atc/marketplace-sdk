<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Authorization;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Categoria de negócio do parceiro (não é categoria de PRODUTO).
 *
 * `id` é INT pequeno aqui — exceção à regra de ID string do TikTok, porque não
 * é snowflake, é o código do tipo de parceiro. Compare por `id`: o `name` muda.
 */
final class BusinessCategory implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $name = null,
    ) {}
}
