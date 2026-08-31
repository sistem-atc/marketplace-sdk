<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Event;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Webhook configurado na loja — item de `data.webhooks[]` de
 * /event/202309/webhooks.
 *
 * NÃO existe id de webhook: a CHAVE é o par (event_type, shop). Por isso o
 * update e o delete são feitos por `event_type`, e cadastrar o mesmo
 * event_type de novo SOBRESCREVE a URL anterior em vez de criar um segundo.
 *
 * Datas são epoch em SEGUNDOS. `updateTime` é o sinal operacional útil: se um
 * evento parou de chegar, comparar a URL registrada aqui com a esperada mostra
 * na hora se alguém repontou o webhook.
 */
final class ShopWebhook implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        // ORDER_STATUS_CHANGE, PACKAGE_UPDATE, RETURN_STATUS_CHANGE, ...
        public readonly ?string $eventType = null,
        // URL que recebe o evento (o `address` da API).
        public readonly ?string $address = null,
        public readonly ?int $createTime = null,
        public readonly ?int $updateTime = null,
    ) {}
}
