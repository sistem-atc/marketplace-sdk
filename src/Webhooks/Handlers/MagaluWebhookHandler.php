<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Webhooks\Handlers;

use Illuminate\Http\Request;
use SistemAtc\Marketplaces\Webhooks\Events\MagaluWebhookEvent;

class MagaluWebhookHandler implements WebhookHandlerInterface
{
    public function validate(Request $request): bool
    {
        return true;
    }

    public function handle(Request $request): void
    {
        $payload = $request->all();

        event(new MagaluWebhookEvent($payload, $this->extractTopic($request, $payload)));
    }

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
