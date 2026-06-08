<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Webhooks\Handlers;

use Illuminate\Http\Request;
use SistemAtc\Marketplaces\Webhooks\Events\MarketplaceWebhookEvent;

class MagaluWebhookHandler implements WebhookHandlerInterface
{
    public function validate(Request $request): bool
    {
        // Assinatura (X-Signature-256 HMAC) e' validada no host antes de delegar.
        return true;
    }

    public function handle(Request $request): void
    {
        $payload = $request->all();

        event(new MarketplaceWebhookEvent('magalu', $payload, $this->extractTopic($request, $payload)));
    }

    /**
     * Topics Magalu: 'orders_order', 'orders_delivery'. Podem vir em
     * type/topic/event_name/event/action (body) ou em headers.
     */
    private function extractTopic(Request $request, array $body): ?string
    {
        $candidates = [
            $body['topic'] ?? null,
            $body['type'] ?? null,
            $body['event_type'] ?? null,
            $body['event_name'] ?? null,
            $body['event'] ?? null,
            $body['action'] ?? null,
            $request->query('topic'),
            $request->header('X-Webhook-Topic'),
            $request->header('X-Event-Type'),
        ];

        foreach ($candidates as $c) {
            if (is_string($c) && $c !== '') {
                return substr($c, 0, 100);
            }
        }

        return null;
    }
}
