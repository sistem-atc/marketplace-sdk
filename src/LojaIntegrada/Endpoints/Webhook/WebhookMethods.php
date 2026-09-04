<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\LojaIntegrada\Endpoints\Webhook;

use SistemAtc\Marketplaces\LojaIntegrada\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;

class WebhookMethods extends BaseMethods
{
    public function list(): array
    {
        return $this->makeRequest(HttpMethod::GET, 'webhook/');
    }

    public function create(string $url, string $event): array
    {
        return $this->makeRequest(HttpMethod::POST, 'webhook/', [], [
            'url' => $url,
            'event' => $event,
        ]);
    }

    public function delete(int|string $id): array
    {
        return $this->makeRequest(HttpMethod::DELETE, "webhook/{$id}/");
    }

    /**
     * Cadastra webhook de PEDIDO (`/webhooks/v1/pedido`, fora do `/v1`). O `token`
     * volta no header das notificações.
     *
     * @return array<string,mixed>
     */
    public function registerOrderWebhook(string $notifyUrl, string $token): array
    {
        return $this->makeRequest(HttpMethod::PUT, $this->rootUrl('webhooks/v1/pedido'), [], ['notifyUrl' => $notifyUrl, 'token' => $token]);
    }

    /** @return array<string,mixed> */
    public function unregisterOrderWebhook(string $notifyUrl, string $token): array
    {
        return $this->makeRequest(HttpMethod::DELETE, $this->rootUrl('webhooks/v1/pedido'), [], ['notifyUrl' => $notifyUrl, 'token' => $token]);
    }

    /** Cadastra webhook de PRODUTO (`/webhooks/v1/produto`). @return array<string,mixed> */
    public function registerProductWebhook(string $notifyUrl, string $token): array
    {
        return $this->makeRequest(HttpMethod::PUT, $this->rootUrl('webhooks/v1/produto'), [], ['notifyUrl' => $notifyUrl, 'token' => $token]);
    }

    /** @return array<string,mixed> */
    public function unregisterProductWebhook(string $notifyUrl, string $token): array
    {
        return $this->makeRequest(HttpMethod::DELETE, $this->rootUrl('webhooks/v1/produto'), [], ['notifyUrl' => $notifyUrl, 'token' => $token]);
    }
}
