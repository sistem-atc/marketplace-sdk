<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\LojaIntegrada\Endpoints\Order;

use SistemAtc\Marketplaces\LojaIntegrada\Bases\BaseMethods;
use SistemAtc\Marketplaces\LojaIntegrada\DTO\Response\Order\OrderResponseDTO;
use SistemAtc\Marketplaces\LojaIntegrada\DTO\Response\Order\OrderSearchResponseDTO;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;

class OrderMethods extends BaseMethods
{
    public function listOrders(int $limit = 50, int $offset = 0, string $orderBy = '-data_criacao', array $extraFilters = []): OrderSearchResponseDTO
    {
        $query = array_merge(['limit' => min($limit, 50), 'offset' => $offset, 'order_by' => $orderBy], $extraFilters);

        return OrderSearchResponseDTO::fromArray($this->makeRequest(HttpMethod::GET, 'pedido/search/', $query));
    }

    public function getOrderByNumero(int|string $numero): OrderResponseDTO
    {
        return OrderResponseDTO::fromArray($this->makeRequest(HttpMethod::GET, "pedido/{$numero}/"));
    }

    public function updateStatus(int|string $numero, string $status): array
    {
        return $this->makeRequest(HttpMethod::PUT, "pedido/{$numero}/", [], [
            'situacao' => $status,
        ]);
    }

    public function updateTracking(int|string $numero, string $trackingCode, string $url = ''): array
    {
        return $this->makeRequest(HttpMethod::POST, "pedido/{$numero}/envio/", [], [
            'objeto' => $trackingCode,
            'url' => $url,
        ]);
    }

    /**
     * Grava o id_externo do pedido (PUT `pedido/{numero}`).
     *
     * @return array<string,mixed>
     */
    public function updateExternalId(int|string $numero, int|string $idExterno): array
    {
        return $this->makeRequest(HttpMethod::PUT, "pedido/{$numero}/", [], ['id_externo' => $idExterno]);
    }

    /**
     * Atualiza o código de rastreio pelo id do ENVIO do pedido (`pedido_envio/{id}`,
     * vem em `envios[].id` do pedido) — contrato atual da API.
     *
     * @return array<string,mixed>
     */
    public function updateShipmentTracking(int|string $pedidoEnvioId, string $trackingCode): array
    {
        return $this->makeRequest(HttpMethod::PUT, "pedido_envio/{$pedidoEnvioId}/", [], ['objeto' => $trackingCode]);
    }

    /**
     * Cria pedido vindo de integração/marketplace (`integration/sales`).
     *
     * @param array<string,mixed> $data buyer{}, shipping{}, amount{}, items[], info{}, integration_data{}
     * @return array<string,mixed>
     */
    public function createIntegrationSale(array $data): array
    {
        return $this->makeRequest(HttpMethod::POST, 'integration/sales/', [], $data);
    }

    /**
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
    public function updateIntegrationSale(int|string $saleId, array $data): array
    {
        return $this->makeRequest(HttpMethod::PUT, "integration/sales/{$saleId}/", [], $data);
    }
}
