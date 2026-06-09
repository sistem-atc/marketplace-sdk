<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Webhooks\Handlers;

use Illuminate\Http\Request;
use SistemAtc\Marketplaces\Webhooks\Events\LojaIntegradaWebhookEvent;

class LojaIntegradaWebhookHandler implements WebhookHandlerInterface
{
    public function validate(Request $request): bool
    {
        return true;
    }

    public function handle(Request $request): void
    {
        $payload = $request->all();
        $topic = is_array($payload) ? ($payload['tipo'] ?? null) : null;

        event(new LojaIntegradaWebhookEvent(
            is_array($payload) ? $payload : [],
            is_string($topic) && $topic !== '' ? $topic : null,
            $this->safeHeaders($request),
            ['tenant' => $this->extractTenant($request)],
        ));
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
