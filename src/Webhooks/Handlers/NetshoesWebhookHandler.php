<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Webhooks\Handlers;

use Illuminate\Http\Request;
use SistemAtc\Marketplaces\Webhooks\Events\NetshoesWebhookEvent;

class NetshoesWebhookHandler implements WebhookHandlerInterface
{
    public function validate(Request $request): bool
    {
        return true;
    }

    public function handle(Request $request): void
    {
        $payload = $request->all();

        event(new NetshoesWebhookEvent($payload, $this->extractTopic($request)));
    }

    private function extractTopic(Request $request): ?string
    {
        $topic = $request->input('topic')
            ?? $request->input('event_type')
            ?? $request->input('type')
            ?? $request->input('action')
            ?? $request->query('topic')
            ?? $request->header('X-Webhook-Topic')
            ?? $request->header('X-Event-Type');

        return is_string($topic) && $topic !== '' ? substr($topic, 0, 100) : null;
    }
}
