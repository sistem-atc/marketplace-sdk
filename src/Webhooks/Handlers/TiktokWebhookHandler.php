<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Webhooks\Handlers;

use Illuminate\Http\Request;
use SistemAtc\Marketplaces\Webhooks\Events\TiktokWebhookEvent;

class TiktokWebhookHandler implements WebhookHandlerInterface
{
    public function validate(Request $request): bool
    {
        return true;
    }

    public function handle(Request $request): void
    {
        $payload = $request->all();
        $type = $payload['type'] ?? null;

        event(new TiktokWebhookEvent($payload, $type !== null ? (string) $type : null));
    }
}
