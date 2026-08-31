<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Conteudo de `data` do webhook TYPE 37 — Product audit status change.
 *
 * Sobrepoe-se ao topico 5 (Product status change): o 37 e' a versao granular e
 * mais nova, com o estado da auditoria por plataforma integrada. Vale assinar
 * os dois so' se o consumidor souber deduplicar.
 */
final class ProductAuditStatusChangeWebhook implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        // Chega como numero no exemplo oficial; guardado como string.
        public readonly ?string $productId = null,
        public readonly ?ProductAuditInfo $audit = null,
        public readonly ?IntegratedPlatformStatus $integratedPlatformStatuses = null,
        public readonly ?int $updateTime = null,
    ) {}
}
