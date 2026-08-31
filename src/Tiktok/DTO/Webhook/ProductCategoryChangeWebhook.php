<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Conteudo de `data` do webhook TYPE 18 — Product category change.
 *
 * Dispara independentemente de quem mudou (vendedor, app ou o proprio TikTok
 * Shop) — inclusive recategorizacao unilateral da plataforma, que muda comissao
 * e regra fiscal sem ninguem do nosso lado ter mexido.
 */
final class ProductCategoryChangeWebhook implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $productId = null,
        public readonly ?string $previousCategoryId = null,
        public readonly ?string $currentCategoryId = null,
        public readonly ?int $updateTime = null,
    ) {}
}
