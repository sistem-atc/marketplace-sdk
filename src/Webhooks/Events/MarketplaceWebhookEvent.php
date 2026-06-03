<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Webhooks\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MarketplaceWebhookEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $marketplace,
        public array $payload,
        public ?string $topic = null
    ) {}
}
