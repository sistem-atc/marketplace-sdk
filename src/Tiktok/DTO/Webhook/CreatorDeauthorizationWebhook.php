<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Miolo de `data` do topico 20 — Creator deauthorization.
 *
 * O criador removeu COMPLETAMENTE o acesso do App aos dados dele. E' o gemeo do
 * topico 6, mas do lado Affiliate: quem some e' um creator, nao uma loja. Por
 * isso o envelope deste topico traz `creator_open_id` e NAO traz `shop_id` — a
 * chave pra invalidar o vinculo esta' no envelope, nao aqui.
 *
 * ⚠️ Divergencia doc x exemplo: a tabela declara `cancel_time` como string, o
 * exemplo oficial manda INT (1644412885). Seguimos o EXEMPLO.
 */
final class CreatorDeauthorizationWebhook implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Epoch em SEGUNDOS do cancelamento. Doc diz string; exemplo manda int. */
        public readonly ?int $cancelTime = null,
    ) {}
}
