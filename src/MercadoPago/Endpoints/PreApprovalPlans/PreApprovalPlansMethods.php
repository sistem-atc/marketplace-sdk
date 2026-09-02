<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\Endpoints\PreApprovalPlans;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\MercadoPago\Bases\BaseMethods;

/**
 * PreApproval Plans — o "molde" de assinatura (valor, frequencia, trial,
 * meios de pagamento aceitos). Assinantes entram por PreApprovalsMethods.
 *
 * Doc: https://www.mercadopago.com.br/developers/pt/reference/subscriptions/_preapproval_plan/post
 */
class PreApprovalPlansMethods extends BaseMethods
{
    /**
     * @param  array<string, mixed>  $payload  reason, auto_recurring, back_url, payment_methods_allowed...
     * @return array<string, mixed>
     */
    public function create(array $payload): array
    {
        return $this->makeRequest(HttpMethod::POST, '/preapproval_plan', body: $payload);
    }

    /**
     * @return array<string, mixed>
     */
    public function get(string $planId): array
    {
        return $this->makeRequest(HttpMethod::GET, '/preapproval_plan/'.rawurlencode($planId));
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function update(string $planId, array $payload): array
    {
        return $this->makeRequest(HttpMethod::PUT, '/preapproval_plan/'.rawurlencode($planId), body: $payload);
    }

    /**
     * Filtros: status, q, sort, limit, offset.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function search(array $filters = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/preapproval_plan/search', $filters);
    }
}
