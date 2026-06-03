<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Webhooks\Handlers;

use Illuminate\Http\Request;

interface WebhookHandlerInterface
{
    public function validate(Request $request): bool;
    public function handle(Request $request): void;
}
