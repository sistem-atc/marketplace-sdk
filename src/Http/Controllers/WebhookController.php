<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use SistemAtc\Marketplaces\Webhooks\Handlers\AmazonWebhookHandler;
use SistemAtc\Marketplaces\Webhooks\Handlers\MercadoLivreWebhookHandler;
use SistemAtc\Marketplaces\Webhooks\Handlers\NetshoesWebhookHandler;
use SistemAtc\Marketplaces\Webhooks\Handlers\ShopeeWebhookHandler;
use SistemAtc\Marketplaces\Webhooks\Handlers\MagaluWebhookHandler;
use SistemAtc\Marketplaces\Webhooks\Handlers\TiktokWebhookHandler;

class WebhookController extends Controller
{
    public function handle(Request $request, string $marketplace)
    {
        // Normaliza slugs com hífen para o nome do handler
        $handler = match ($marketplace) {
            'mercadolivre', 'mercado-livre' => new MercadoLivreWebhookHandler(),
            'shopee' => new ShopeeWebhookHandler(),
            'magalu' => new MagaluWebhookHandler(),
            'tiktok' => new TiktokWebhookHandler(),
            'amazon' => new AmazonWebhookHandler(),
            'netshoes' => new NetshoesWebhookHandler(),
            default => null,
        };

        if (!$handler) {
            // No Bunker, se não achar o handler, talvez queira apenas ignorar com 200
            return response()->json(['error' => 'Unsupported marketplace'], 200);
        }

        if (!$handler->validate($request)) {
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $handler->handle($request);

        return response()->json(['ok' => true]);
    }
}
