<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\Endpoints\AuthorizedPayments;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\MercadoPago\Bases\BaseMethods;

/**
 * Authorized Payments — cada cobranca recorrente gerada por uma assinatura
 * (o SDK oficial chama de `Invoice`). Liga a preapproval ao payment de fato
 * via `payment.id`.
 *
 * Doc: https://www.mercadopago.com.br/developers/pt/reference/subscriptions/_authorized_payments_id/get
 */
class AuthorizedPaymentsMethods extends BaseMethods
{
    /**
     * @return array<string, mixed>
     */
    public function get(int|string $authorizedPaymentId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/authorized_payments/{$authorizedPaymentId}");
    }

    /**
     * Filtros: preapproval_id, payer_id, status, sort, limit, offset.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function search(array $filters = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/authorized_payments/search', $filters);
    }
}
