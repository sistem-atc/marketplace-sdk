<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Webhooks\Handlers;

use Illuminate\Http\Request;
use SistemAtc\Marketplaces\Webhooks\Events\ShopeeWebhookEvent;

class ShopeeWebhookHandler implements WebhookHandlerInterface
{
    public function validate(Request $request): bool
    {
        return true;
    }

    public function handle(Request $request): void
    {
        $payload = $request->all();
        $code = $payload['code'] ?? null;

        event(new ShopeeWebhookEvent($payload, $code !== null ? (string) $code : null));
    }
}
