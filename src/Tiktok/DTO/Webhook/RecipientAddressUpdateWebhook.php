<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` do webhook type 3 — Recipient address update.
 *
 * So' avisa QUE o endereco mudou; o endereco novo nao vem no payload — precisa
 * de um Get Order Detail. Importa pro fiscal: se a NF-e ja' foi emitida com o
 * destinatario antigo, esse aviso e' o sinal de carta de correcao/reemissao.
 *
 * Quirk: o exemplo oficial deste topico NAO traz `tts_notification_id` (todos
 * os outros trazem). A dedup, aqui, tem que cair no par order_id+update_time.
 */
final class RecipientAddressUpdateWebhook implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $orderId = null,
        /** Epoch em SEGUNDOS de quando o endereco foi atualizado. */
        public readonly ?int $updateTime = null,
    ) {}
}
