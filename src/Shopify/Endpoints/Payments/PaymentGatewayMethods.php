<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\Endpoints\Payments;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopify\Bases\BaseMethods;

/**
 * Payment Gateways (`payment_gateways`). Recurso legado (removido das versoes
 * novas da Admin API); mantido pela lista da lib oficial — pode responder 404
 * dependendo da loja/versao.
 */
class PaymentGatewayMethods extends BaseMethods
{
    /**
     * Lista os gateways de pagamento.
     */
    public function list(): array
    {
        return $this->makeRequest(HttpMethod::GET, '/payment_gateways');
    }

    /**
     * Recupera um gateway.
     */
    public function get(int|string $gatewayId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/payment_gateways/{$gatewayId}");
    }

    /**
     * Cria um gateway. Embrulha em `payment_gateway`.
     *
     * @param  array<string, mixed>  $gateway
     */
    public function create(array $gateway): array
    {
        return $this->makeRequest(HttpMethod::POST, '/payment_gateways', [], ['payment_gateway' => $gateway]);
    }

    /**
     * Atualiza um gateway. Embrulha em `payment_gateway`.
     *
     * @param  array<string, mixed>  $gateway
     */
    public function update(int|string $gatewayId, array $gateway): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/payment_gateways/{$gatewayId}", [], ['payment_gateway' => $gateway]);
    }

    /**
     * Exclui um gateway.
     */
    public function delete(int|string $gatewayId): array
    {
        return $this->makeRequest(HttpMethod::DELETE, "/payment_gateways/{$gatewayId}");
    }
}
