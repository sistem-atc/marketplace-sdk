<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` do webhook type 4 — Package update.
 *
 * Combine/split de pacote e' o que remonta a relacao pedido<->pacote DEPOIS do
 * pedido criado. Impacto direto na expedicao e no fiscal: um SPLIT depois da
 * NF-e emitida quebra a amarracao nota<->pacote; um COMBINE junta pedidos que
 * saem na mesma etiqueta.
 *
 * @property list<PackageUpdateItem>|null $packageList
 */
final class PackageUpdateWebhook implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /**
         * COMBINE | CANCEL_COMBINE | SPLIT | CANCEL_SPLIT | ADDRESS_UPDATE_SPLIT |
         * CANCEL_FULFILL_SPLIT | FULFILL_UNCOMBINE | PARTLY_CANCEL_SPLIT |
         * SPLIT_BY_SKU_CANCEL.
         */
        public readonly ?string $scType = null,
        /** Quem operou: ROLE_USER | ROLE_SELLER | ROLE_OPERATOR | ROLE_SYSTEM. */
        public readonly ?string $roleType = null,
        #[ArrayOf(PackageUpdateItem::class)]
        public readonly ?array $packageList = null,
        /** Epoch em SEGUNDOS da atualizacao do pacote. */
        public readonly ?int $updateTime = null,
    ) {}
}
