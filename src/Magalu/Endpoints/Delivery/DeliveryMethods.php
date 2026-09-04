<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\Endpoints\Delivery;

use SistemAtc\Marketplaces\Magalu\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Magalu\DTO\Response\Delivery\Delivery;

class DeliveryMethods extends BaseMethods
{
    public function listDeliveries(int $limit = 50, int $offset = 0, ?string $status = null): array
    {
        $query = ['limit' => $limit, '_offset' => $offset];
        if ($status) $query['status'] = $status;
        return $this->makeRequest(HttpMethod::GET, '/seller/v1/deliveries', $query);
    }

    public function getDelivery(string $deliveryId): Delivery
    {
        return Delivery::fromArray($this->makeRequest(HttpMethod::GET, "/seller/v1/deliveries/{$deliveryId}"));
    }

    public function getDeliveryHistory(string $deliveryId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/seller/v1/deliveries/{$deliveryId}/histories");
    }


    /**
     * Busca de entregas com filtros (GET /seller/v1/deliveries).
     *
     * Filtros: `code` (pedido), `id`, `status` (new|approved|invoiced|shipped|
     * delivered|cancelled...), `purchased_at__ge|le`; `_limit` (default 20),
     * `_offset`, `_sort` (`purchased_at:asc|desc`).
     *
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function search(array $filters = [], int $limit = 20, int $offset = 0, ?string $sort = null): array
    {
        $query = array_merge($filters, ['_limit' => $limit, '_offset' => $offset]);
        if ($sort !== null) $query['_sort'] = $sort;

        return $this->makeRequest(HttpMethod::GET, '/seller/v1/deliveries', $query);
    }

    /**
     * Entrega crua (array) — GET /seller/v1/deliveries/{id}, com `updated_at__ge` opcional.
     *
     * @return array<string, mixed>
     */
    public function getDeliveryRaw(string $deliveryId, ?string $updatedAtGe = null): array
    {
        $query = $updatedAtGe !== null ? ['updated_at__ge' => $updatedAtGe] : [];

        return $this->makeRequest(HttpMethod::GET, "/seller/v1/deliveries/{$deliveryId}", $query);
    }

    /**
     * Historico paginado da entrega (GET /seller/v1/deliveries/{id}/histories).
     * `_sort`: `created_at:asc|desc`; filtros `created_at__ge|le`.
     *
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function listHistories(string $deliveryId, array $filters = [], int $limit = 20, int $offset = 0, ?string $sort = null): array
    {
        $query = array_merge($filters, ['_limit' => $limit, '_offset' => $offset]);
        if ($sort !== null) $query['_sort'] = $sort;

        return $this->makeRequest(HttpMethod::GET, "/seller/v1/deliveries/{$deliveryId}/histories", $query);
    }

    /**
     * Informa o despacho (POST /seller/v1/deliveries/{id}/shippings).
     *
     * Body: `carrier.name`, `channel.id` (+ `extras`), `dates.shipped_at`,
     * `dates.estimated_delivery_at`, `labels[] {id, value}`, `protocol`, `tracking_url`.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function registerShipping(string $deliveryId, array $data): array
    {
        return $this->makeRequest(HttpMethod::POST, "/seller/v1/deliveries/{$deliveryId}/shippings", [], $data);
    }

    /**
     * Confirma a entrega ao cliente (POST /seller/v1/deliveries/{id}/finishing).
     *
     * @param array<string, string> $channelExtras
     * @return array<string, mixed>
     */
    public function finish(string $deliveryId, string $channelId, ?string $deliveredAt = null, array $channelExtras = []): array
    {
        $body = ['channel' => ['id' => $channelId]];
        if ($channelExtras) $body['channel']['extras'] = $channelExtras;
        if ($deliveredAt !== null) $body['delivered_at'] = $deliveredAt;

        return $this->makeRequest(HttpMethod::POST, "/seller/v1/deliveries/{$deliveryId}/finishing", [], $body);
    }
}
