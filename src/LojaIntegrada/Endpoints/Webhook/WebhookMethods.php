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
}
