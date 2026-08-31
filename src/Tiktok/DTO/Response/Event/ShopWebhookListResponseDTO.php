<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Event;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` do Get Shop Webhooks. Sem paginação: a resposta traz TODOS os
 * webhooks da loja de uma vez, e `totalCount` é a contagem da lista.
 *
 * @property list<ShopWebhook>|null $webhooks
 */
final class ShopWebhookListResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(ShopWebhook::class)]
        public readonly ?array $webhooks = null,
        public readonly ?int $totalCount = null,
    ) {}
}
