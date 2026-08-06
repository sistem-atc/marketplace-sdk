<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\ConectaLa\Endpoints\Order;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\ConectaLa\Bases\BaseMethods;

/**
 * Pedidos (Conecta Lá / Shophub). A "fila" (`/Orders`) é o modelo de sincronismo
 * da plataforma: pedidos novos entram na fila, você processa e remove da fila.
 * `/Orders/list` é a listagem paginada geral.
 */
class OrderMethods extends BaseMethods
{
    /** Fila de pedidos novos (GET /Orders). `$filters` ex.: ['new_order' => 1]. */
    public function queue(array $filters = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/Orders', $filters);
    }

    /** Lista paginada de pedidos (GET /Orders/list). */
    public function list(int $page = 1, int $perPage = 50, array $filters = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/Orders/list', array_merge([
            'page' => $page,
            'per_page' => $perPage,
        ], $filters));
    }

    /** Detalhe de um pedido (GET /Orders/{order}). */
    public function get(string $orderId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/Orders/{$orderId}");
    }

    /** Remove o pedido da fila (DELETE /Orders/{order}) — marca como processado. */
    public function removeFromQueue(string $orderId): array
    {
        return $this->makeRequest(HttpMethod::DELETE, "/Orders/{$orderId}");
    }

    /** Envia a NFe do pedido (POST /Orders/nfe). `$nfe` = payload da nota. */
    public function createNfe(array $nfe): array
    {
        return $this->makeRequest(HttpMethod::POST, '/Orders/nfe', body: $nfe);
    }

    /** Marca ENVIADO (PUT /Orders/{order}/shipped). `$data` ex.: rastreio/transportadora. */
    public function markShipped(string $orderId, array $data = []): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/Orders/{$orderId}/shipped", body: $data);
    }

    /** Marca ENTREGUE (PUT /Orders/{order}/delivered). */
    public function markDelivered(string $orderId, array $data = []): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/Orders/{$orderId}/delivered", body: $data);
    }

    /** Marca CANCELADO (PUT /Orders/{order}/canceled). */
    public function markCanceled(string $orderId, array $data = []): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/Orders/{$orderId}/canceled", body: $data);
    }
}
