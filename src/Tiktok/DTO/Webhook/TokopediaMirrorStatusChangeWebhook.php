<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Conteudo de `data` do webhook TYPE 35 — Tokopedia mirror status change.
 *
 * So' existe no mercado da INDONESIA (espelhamento TikTok Shop -> Tokopedia).
 * Envelope traz `seller_open_id`; o shop id vem AQUI dentro, como
 * `tts_shop_id` — nao no envelope.
 */
final class TokopediaMirrorStatusChangeWebhook implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $ttsShopId = null,
        // MIRRORED | ROLLED_BACK | CANCELED
        public readonly ?string $mirrorStatus = null,
        public readonly ?int $updateTime = null,
    ) {}
}
