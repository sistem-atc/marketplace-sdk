<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\Endpoints\AuthorizedPayments;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\MercadoPago\Bases\BaseMethods;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\AuthorizedPayments\AuthorizedPaymentResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\AuthorizedPayments\AuthorizedPaymentSearchResponseDTO;

/**
 * Authorized Payments — cada cobranca recorrente gerada por uma assinatura
 * (o SDK oficial chama de `Invoice`). Liga a preapproval ao payment de fato
 * via `payment.id`.
 *
 * Doc: https://www.mercadopago.com.br/developers/pt/reference/subscriptions/_authorized_payments_id/get
 */
class AuthorizedPaymentsMethods extends BaseMethods
{
    public function get(int|string $authorizedPaymentId): AuthorizedPaymentResponseDTO
    {
        return AuthorizedPaymentResponseDTO::fromArray($this->makeRequest(HttpMethod::GET, "/authorized_payments/{$authorizedPaymentId}"));
    }

    /**
     * Filtros: preapproval_id, payer_id, status, sort, limit, offset.
     *
     * @param  array<string, mixed>  $filters
     */
    public function search(array $filters = []): AuthorizedPaymentSearchResponseDTO
    {
        return AuthorizedPaymentSearchResponseDTO::fromArray($this->makeRequest(HttpMethod::GET, '/authorized_payments/search', $filters));
    }
}
