<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Webhooks\Handlers;

use Illuminate\Http\Request;
use SistemAtc\Marketplaces\Contracts\ShopeeWebhookVerifier;
use SistemAtc\Marketplaces\Webhooks\Events\ShopeeWebhookEvent;

class ShopeeWebhookHandler implements WebhookHandlerInterface
{
    /**
     * Delega a verificacao HMAC pro verifier vinculado pelo host (que tem o
     * Partner Key por-shop). Sem verifier vinculado, mantem o comportamento
     * legado (aceita) pra nao quebrar quem ainda nao implementou.
     */
    public function validate(Request $request): bool
    {
        if (app()->bound(ShopeeWebhookVerifier::class)) {
            return app(ShopeeWebhookVerifier::class)->verify($request);
        }

        return true;
    }

    public function handle(Request $request): void
    {
        $payload = $request->all();
        $code = $payload['code'] ?? null;

        event(new ShopeeWebhookEvent($payload, $code !== null ? (string) $code : null));
    }
}
