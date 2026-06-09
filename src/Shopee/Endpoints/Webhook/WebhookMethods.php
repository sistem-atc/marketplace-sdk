<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\Endpoints\Webhook;

use SistemAtc\Marketplaces\Shopee\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;

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
}
