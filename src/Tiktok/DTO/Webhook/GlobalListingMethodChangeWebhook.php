<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Conteudo de `data` do webhook TYPE 52 — Global listing method change.
 *
 * So' pra seller global; envelope traz `seller_open_id` e NAO traz `shop_id`.
 *
 * Cuidado com o nome: `localProductId` e' LISTA de ids (a doc tipa []string e o
 * exemplo confirma), apesar do singular.
 */
final class GlobalListingMethodChangeWebhook implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param list<string>|null $localProductId */
    public function __construct(
        public readonly ?string $globalProductId = null,
        public readonly ?array $localProductId = null,
        // GLOBAL_PUBLISHING (cria global e publica nos mercados) |
        // LOCAL_REPLICATION (cria local e replica pros outros)
        public readonly ?string $listingMethod = null,
    ) {}
}
