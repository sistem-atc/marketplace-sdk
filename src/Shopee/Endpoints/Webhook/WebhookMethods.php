<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\Endpoints\Webhook;

use SistemAtc\Marketplaces\Shopee\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;

/**
 * Webhook / Push (api_type Public) — configuracao do push do APP, assinada
 * so' com partner_id + path + timestamp (vale pra todas as lojas do app).
 */
class WebhookMethods extends BaseMethods
{
    public function getWebhookSetting(): array
    {
        // Webhook management is usually done with Partner level auth (publicApi = true in some contexts, but here it depends on implementation)
        return $this->makeRequest(HttpMethod::GET, '/api/v2/webhook/get_webhook_setting', [], [], true);
    }

    public function updateWebhookSetting(array $data): array
    {
        return $this->makeRequest(HttpMethod::POST, '/api/v2/webhook/update_webhook_setting', [], $data, true);
    }

    /**
     * Configura callback_url e liga/desliga tipos de push do app.
     * Codigos: 1=autorizacao 2=desautorizacao 3=status do pedido 4=tracking
     * 5=Shopee updates 6=item banido 7=ponto de contato 8=Shopee updates
     * 9=reserved 10=chat… (lista completa no doc v2.push.set_app_push_config).
     * blocked_shop_id_list: ate' 500 lojas que NAO recebem push.
     *
     * @param int[] $on
     * @param int[] $off
     * @param int[] $blockedShopIds
     */
    public function setAppPushConfig(?string $callbackUrl = null, array $on = [], array $off = [], array $blockedShopIds = []): array
    {
        $body = [];
        if ($callbackUrl !== null) {
            $body['callback_url'] = $callbackUrl;
        }
        if ($on !== []) {
            $body['set_push_config_on'] = array_values($on);
        }
        if ($off !== []) {
            $body['set_push_config_off'] = array_values($off);
        }
        if ($blockedShopIds !== []) {
            $body['blocked_shop_id_list'] = array_values($blockedShopIds);
        }

        return $this->makeRequest(HttpMethod::POST, '/api/v2/push/set_app_push_config', [], $body, true);
    }

    /** callback_url + status (on/off) por tipo de push + lojas bloqueadas. */
    public function getAppPushConfig(): array
    {
        return $this->makeRequest(HttpMethod::GET, '/api/v2/push/get_app_push_config', [], [], true);
    }

    /**
     * Pushes que a Shopee nao conseguiu entregar (callback fora do ar). Devolve
     * ate' 100 mensagens + last_message_id + has_next_page; apos processar,
     * confirme com confirmConsumedLostPushMessage() senao voltam na proxima.
     */
    public function getLostPushMessage(): array
    {
        return $this->makeRequest(HttpMethod::GET, '/api/v2/push/get_lost_push_message', [], [], true);
    }

    /** Marca como consumidas as mensagens ate' last_message_id (do getLostPushMessage). */
    public function confirmConsumedLostPushMessage(int $lastMessageId): array
    {
        return $this->makeRequest(HttpMethod::POST, '/api/v2/push/confirm_consumed_lost_push_message', [], [
            'last_message_id' => $lastMessageId,
        ], true);
    }
}
