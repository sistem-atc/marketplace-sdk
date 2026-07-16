<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Shared\Common;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Bloco `paging` das buscas paginadas do ML (`total`/`offset`/`limit`).
 */
final class Paging implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $total = null,
        public readonly ?int $offset = null,
        public readonly ?int $limit = null,
    ) {}
}
