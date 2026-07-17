<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Promotion;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Wrapper paginado das promoções — `list()` (campanhas do seller) e
 * `listItems()` (itens de uma campanha). `paging` do listItems traz o cursor
 * `search_after`; shape volátil → cru.
 *
 * @property list<Promotion> $results
 */
final class PromotionListResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param list<Promotion> $results */
    public function __construct(
        #[ArrayOf(Promotion::class)]
        public readonly array $results = [],
        public readonly mixed $paging = null,
    ) {}
}
