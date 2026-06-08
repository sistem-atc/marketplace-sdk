<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Webhooks\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

abstract class AbstractWebhookEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public array $payload,
        public ?string $topic = null,
    ) {}
}
