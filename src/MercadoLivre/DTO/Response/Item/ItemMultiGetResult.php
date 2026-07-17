<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Item;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Elemento do multiget (GET /items?ids=...): o ML envelopa cada anúncio em
 * `{code, body}` — `code` é o HTTP daquele id (200/404/403), `body` o anúncio.
 *
 * Consumidor: `if ($r->code !== 200 || ! $r->body?->id) continue;`
 */
final class ItemMultiGetResult implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $code = null,
        public readonly ?ItemResponseDTO $body = null,
    ) {}
}
