<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data.replicated_product` do webhook TYPE 51 — o produto criado no mercado de
 * destino.
 *
 * `failReasons` fica como array CRU de proposito: a tabela documenta
 * `[]object` (lista de {message}) mas o exemplo oficial manda um OBJETO unico
 * `{"message": ""}`. Tipar como #[ArrayOf(...)] estouraria a hidratacao na
 * forma do exemplo; cru, as duas formas sobrevivem inteiras ao roundtrip.
 */
final class ReplicatedProduct implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param array<mixed>|null $failReasons */
    public function __construct(
        public readonly ?string $productId = null,
        // Mercado de destino (GB, US, ...).
        public readonly ?string $region = null,
        // SUCCESS | DRAFT (caiu como rascunho por erro de validacao) | FAILED
        public readonly ?string $result = null,
        public readonly ?array $failReasons = null,
    ) {}
}
