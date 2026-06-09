<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\Endpoints\Order;

use SistemAtc\Marketplaces\Shopify\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;

class OrderMethods extends BaseMethods
{
    /**
     * Recupera detalhes de um pedido pelo ID do Shopify.
     */
    public function get(int|string $orderId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/orders/{$orderId}");
    }

    /**
     * Busca um pedido pelo numero (ex: #1001 ou 1001).
     */
    public function getByNumber(string $orderNumber): array
    {
        // Garante o prefixo # se nao houver, pois o Shopify armazena como #1001
        if (! str_starts_with($orderNumber, '#')) {
            // Alguns integradores mandam sem o #. Tentamos buscar de ambas as formas se necessario,
            // mas o parametro 'name' geralmente espera o formato exato.
        }
        
        $params = [
            'name' => $orderNumber,
            'status' => 'any',
        ];

        $response = $this->makeRequest(HttpMethod::GET, '/orders', $params);
        return $response['orders'][0] ?? [];
    }

    /**
     * Lista pedidos por periodo.
     * Formato data: ISO 8601 (ex: 2024-04-01T00:00:00-04:00)
     */
    public function listByPeriod(string $startDate, string $endDate, array $extraParams = []): array
    {
        $params = array_merge([
            'created_at_min' => $startDate,
            'created_at_max' => $endDate,
            'status' => 'any',
        ], $extraParams);

        return $this->makeRequest(HttpMethod::GET, '/orders', $params);
    }

    /**
     * Lista pedidos (paginado).
     */
    public function list(array $params = []): array
    {
        $params = array_merge(['status' => 'any'], $params);
        return $this->makeRequest(HttpMethod::GET, '/orders', $params);
    }

    /**
     * Cancela um pedido.
     */
    public function cancel(int|string $orderId, array $data = []): array
    {
        return $this->makeRequest(HttpMethod::POST, "/orders/{$orderId}/cancel", [], $data);
    }

    /**
     * Fecha um pedido.
     */
    public function close(int|string $orderId): array
    {
        return $this->makeRequest(HttpMethod::POST, "/orders/{$orderId}/close");
    }

    /**
     * Abre um pedido fechado.
     */
    public function open(int|string $orderId): array
    {
        return $this->makeRequest(HttpMethod::POST, "/orders/{$orderId}/open");
    }
}
