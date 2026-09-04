<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\Endpoints\Payments;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopify\Bases\BaseMethods;

/**
 * Shopify Payments: saldo e disputas (chargebacks/inquiries).
 *
 * Recursos REST: `balance`, `dispute`. Exigem escopo `read_shopify_payments_disputes`
 * / `read_shopify_payments_payouts` e a loja habilitada no Shopify Payments.
 */
class ShopifyPaymentsMethods extends BaseMethods
{
    /**
     * Saldo atual da conta Shopify Payments.
     */
    public function balance(): array
    {
        return $this->makeRequest(HttpMethod::GET, '/shopify_payments/balance');
    }

    /**
     * Lista as disputas.
     *
     * @param  array<string, mixed>  $params  since_id, last_id, status, initiated_at
     */
    public function listDisputes(array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/shopify_payments/disputes', $params);
    }

    /**
     * Recupera uma disputa.
     */
    public function getDispute(int|string $disputeId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/shopify_payments/disputes/{$disputeId}");
    }
}
