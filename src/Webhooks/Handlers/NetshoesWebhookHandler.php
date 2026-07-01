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

        // Payload confirmado da notificacao Netshoes PEDIDO_ALTERADO:
        //   { sellerId, name, eventCode:"PEDIDO_ALTERADO", eventAction:"PEDIDO",
        //     eventMessage, eventDate, urlNotifyService,
        //     parameters:{ orderNumber }, expands:[], links:[] }
        // Topic = eventCode; params expostos pro listener do host.
        event(new NetshoesWebhookEvent(
            payload: $payload,
            topic: $this->extractTopic($request, $payload),
            headers: [],
            params: is_array($payload['parameters'] ?? null) ? $payload['parameters'] : [],
        ));
    }

    /**
     * @param  array<string, mixed>  $body
     */
    private function extractTopic(Request $request, array $body): ?string
    {
        $candidates = [
            $body['eventCode'] ?? null,   // PEDIDO_ALTERADO (confirmado)
            $body['eventAction'] ?? null, // PEDIDO
            $body['topic'] ?? null,
            $body['event_type'] ?? null,
            $body['type'] ?? null,
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
