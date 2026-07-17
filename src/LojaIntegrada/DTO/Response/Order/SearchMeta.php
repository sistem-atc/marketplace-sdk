<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\LojaIntegrada\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Bloco `meta` do search Tastypie da Loja Integrada. */
final class SearchMeta implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $limit = null,
        public readonly ?int $offset = null,
        public readonly ?int $totalCount = null,
        public readonly ?string $next = null,
        public readonly ?string $previous = null,
    ) {}
}
