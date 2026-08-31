<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` do webhook type 58 — FBT MCF order status.
 *
 * Fiscal: saida de mercadoria NOSSA a partir do armazem do TikTok pra um pedido
 * de OUTRO canal. Quem emite a NF-e de venda somos nos (o TikTok so' expede),
 * entao SHIPPED/HANDOVER e' o gatilho de emissao; ABNORMAL/LOST/DAMAGE sao
 * perda em poder de terceiro — sinistro, nao venda.
 *
 * Divergencia: o exemplo oficial identifica o vendedor por `creator_open_id`,
 * a tabela diz `seller_open_id`. Os dois vivem no WebhookEnvelope.
 */
final class FbtMcfOrderStatusWebhook implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?McfOrder $mcfOrder = null,
    ) {}
}
