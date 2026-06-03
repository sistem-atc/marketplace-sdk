<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Webhooks\Handlers;

use Illuminate\Http\Request;
use SistemAtc\Marketplaces\Webhooks\Events\MarketplaceWebhookEvent;

class ShopeeWebhookHandler implements WebhookHandlerInterface
{
    public function validate(Request $request): bool
    {
        // Shopee envia assinatura para validar se o push é autêntico
        return true; 
    }

    public function handle(Request $request): void
    {
        $payload = $request->all();
        $code = $payload['code'] ?? null;
        
        event(new MarketplaceWebhookEvent('shopee', $payload, (string) $code));
    }
}
