<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\Endpoints\Payments;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopify\Bases\BaseMethods;

/**
 * Payments de checkout (`checkouts/{checkout_token}/payments`) — Checkout API
 * (apps de venda / sales channel). Deprecado desde 2024-04, ainda responde.
 */
class PaymentMethods extends BaseMethods
{
    /**
     * Recupera um pagamento de um checkout.
     */
    public function get(int|string $checkoutId, int|string $paymentId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/checkouts/{$checkoutId}/payments/{$paymentId}");
    }

    /**
     * Cria um pagamento num checkout. Embrulha em `payment` — ex.:
     * ['request_details' => [...], 'amount' => ..., 'session_id' => ..., 'unique_token' => ...].
     *
     * @param  array<string, mixed>  $payment
     */
    public function create(int|string $checkoutId, array $payment): array
    {
        return $this->makeRequest(HttpMethod::POST, "/checkouts/{$checkoutId}/payments", [], ['payment' => $payment]);
    }
}
