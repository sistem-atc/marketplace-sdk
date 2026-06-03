<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Webhooks\Handlers;

use Illuminate\Http\Request;
use SistemAtc\Marketplaces\Webhooks\Events\MarketplaceWebhookEvent;

class MercadoLivreWebhookHandler implements WebhookHandlerInterface
{
    public function validate(Request $request): bool
    {
        // No Bunker atual, a validacao e' ignorada ou baseada em IP se necessario.
        // O ML permite configurar um Client Secret para assinar payloads (HMAC).
        return true; 
    }

    public function handle(Request $request): void
    {
        $payload = $request->all();
        $topic = $payload['topic'] ?? $payload['resource'] ?? 'unknown';

        // Dispara o evento que o Bunker vai ouvir para mandar direto para a fila.
        event(new MarketplaceWebhookEvent('mercadolivre', $payload, (string) $topic));
    }
}
