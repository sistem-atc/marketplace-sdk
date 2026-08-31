<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\Endpoints\Event;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Tiktok\Bases\BaseMethods;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Event\ShopWebhook;

/**
 * Event API do TikTok Shop (/event/{version}/webhooks) — gestão dos webhooks
 * da LOJA pela API, sem passar pelo App Console.
 *
 * Modelo mental: não existe id de webhook. A chave é (loja, event_type), e o
 * endpoint de update é um UPSERT — cadastrar um event_type que já existe
 * SOBRESCREVE a URL, não cria um segundo registro. Consequência prática: pra
 * "mover" um evento de URL basta chamar updateWebhook; pra desligar de vez,
 * deleteWebhook.
 *
 * Valor operacional: com getWebhooks dá pra auditar, em segundos, se um evento
 * parou de chegar porque a URL registrada na loja não é mais a nossa.
 *
 * ATENÇÃO ao verbo: a tabela da doc diz "POST" pros três, mas os exemplos de
 * curl e todos os SDKs oficiais usam GET / PUT / DELETE. Vale o verbo do curl —
 * POST em /event/202309/webhooks não é o update.
 */
class EventMethods extends BaseMethods
{
    private const DEFAULT_VERSION = '202309';

    /**
     * Webhooks configurados na loja (`data.webhooks[]`). Sem paginação.
     *
     * @return list<ShopWebhook>
     */
    public function getWebhooks(string $version = self::DEFAULT_VERSION): array
    {
        $response = $this->makeRequest(HttpMethod::GET, "/event/{$version}/webhooks");

        return array_map(
            static fn (array $w): ShopWebhook => ShopWebhook::fromArray($w),
            $response['data']['webhooks'] ?? [],
        );
    }

    /**
     * Cria OU reaponta o webhook de um event_type (upsert por event_type).
     *
     * @param  string  $address  URL que recebe o evento — máx. 255 caracteres.
     * @param  string  $eventType  ORDER_STATUS_CHANGE, PACKAGE_UPDATE, RETURN_STATUS_CHANGE, ...
     * @return array<string, mixed> `data` vem vazio em caso de sucesso.
     */
    public function updateWebhook(
        string $address,
        string $eventType,
        string $version = self::DEFAULT_VERSION,
    ): array {
        return $this->makeRequest(
            HttpMethod::PUT,
            "/event/{$version}/webhooks",
            [],
            ['address' => $address, 'event_type' => $eventType],
        );
    }

    /**
     * Remove o webhook de um event_type. A loja para de receber esse evento —
     * não há como reenviar o que foi perdido no intervalo.
     *
     * @return array<string, mixed> `data` vem vazio em caso de sucesso.
     */
    public function deleteWebhook(string $eventType, string $version = self::DEFAULT_VERSION): array
    {
        return $this->makeRequest(
            HttpMethod::DELETE,
            "/event/{$version}/webhooks",
            [],
            ['event_type' => $eventType],
        );
    }
}
