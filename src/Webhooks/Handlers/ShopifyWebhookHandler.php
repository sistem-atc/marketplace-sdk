<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Webhooks\Handlers;

use Illuminate\Http\Request;
use SistemAtc\Marketplaces\Webhooks\Events\ShopifyWebhookEvent;

class ShopifyWebhookHandler implements WebhookHandlerInterface
{
    public function validate(Request $request): bool
    {
        return true;
    }

    public function handle(Request $request): void
    {
        $payload = $request->all();

        event(new ShopifyWebhookEvent(
            $payload,
            $this->extractTopic($request),
            $this->safeHeaders($request),
            ['tenant' => $this->extractTenant($request)],
        ));
    }

    private function extractTopic(Request $request): ?string
    {
        $topic = $request->header('X-Shopify-Topic')
            ?? $request->input('topic')
            ?? $request->input('event_type')
            ?? $request->input('type')
            ?? $request->query('topic');

        return is_string($topic) && $topic !== '' ? substr($topic, 0, 100) : null;
    }

    private function extractTenant(Request $request): ?string
    {
        $tenant = $request->route('tenant');

        return is_string($tenant) && $tenant !== '' ? $tenant : null;
    }

    private function safeHeaders(Request $request): array
    {
        $sensitive = ['authorization', 'cookie', 'set-cookie'];
        $out = [];
        foreach ($request->headers->all() as $name => $values) {
            if (in_array(strtolower($name), $sensitive, true)) {
                continue;
            }
            $out[$name] = is_array($values) ? ($values[0] ?? '') : (string) $values;
        }

        return $out;
    }
}
