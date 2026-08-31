<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de `global_listing_rules` (v202507) — diz COMO a loja pode listar
 * e como o estoque e' alocado entre mercados.
 *
 * `listingMethods` e' tipado `array|string`: a doc declara []string mas o
 * exemplo oficial devolve a string crua ("GLOBAL_PUBLISHING"). Aceitar as
 * duas formas evita perder o valor quando a API manda escalar.
 * Valores: GLOBAL_PUBLISHING (cria global, publica nos mercados) e
 * LOCAL_REPLICATION (cria local, replica pros outros).
 *
 * @property list<GlobalInventoryRule>|null $inventoryRules
 */
final class GlobalListingRulesResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly array|string|null $listingMethods = null,
        #[ArrayOf(GlobalInventoryRule::class)]
        public readonly ?array $inventoryRules = null,
    ) {}
}
