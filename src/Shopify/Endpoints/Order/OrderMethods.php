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

    /**
     * Itera TODOS os pedidos do periodo seguindo a paginacao por CURSOR da
     * Admin API REST (Link header -> page_info). Rende um pedido (array) por vez.
     *
     * Necessario porque `list()`/`listByPeriod()` devolvem so' 1 pagina (max 250):
     * o `makeRequest` retorna apenas o corpo JSON e descarta o `Link` header. Aqui
     * usamos o httpClient direto pra ler o header.
     *
     * Regras da Shopify:
     *   - A 1a pagina leva os filtros (created_at_min/max + status).
     *   - As paginas seguintes SO' podem levar `page_info` + `limit` (qualquer
     *     outro filtro junto com page_info = 400 Bad Request).
     *
     * Formato data: ISO 8601 (ex: 2024-04-01T00:00:00-03:00).
     *
     * @param  array<string, mixed>  $extraParams  filtros extras so' na 1a pagina
     * @return \Generator<int, array<string, mixed>>
     */
    public function eachByPeriod(
        string $startDate,
        string $endDate,
        int $limit = 250,
        array $extraParams = []
    ): \Generator {
        $query = array_merge([
            'created_at_min' => $startDate,
            'created_at_max' => $endDate,
            'status' => 'any',
            'limit' => $limit,
        ], $extraParams);

        $attempt = 0;
        while (true) {
            $response = $this->httpClient->get('/orders.json', $query);

            // Retry simples em 429/5xx (mesma politica do makeRequest).
            if (($response->status() === 429 || $response->status() >= 500) && $attempt < 3) {
                $attempt++;
                sleep((int) ($response->header('Retry-After') ?: 2 ** $attempt));

                continue;
            }
            $attempt = 0;

            if ($response->failed()) {
                $this->handleError($response);
            }

            foreach (($response->json('orders') ?? []) as $order) {
                yield $order;
            }

            $pageInfo = $this->nextPageInfo($response->header('Link'));
            if ($pageInfo === null) {
                break;
            }

            // page_info sozinho — a Shopify rejeita filtros junto com o cursor.
            $query = ['limit' => $limit, 'page_info' => $pageInfo];
        }
    }

    /**
     * Extrai o `page_info` do segmento rel="next" do Link header. null = fim.
     */
    private function nextPageInfo(?string $linkHeader): ?string
    {
        if ($linkHeader === null || $linkHeader === '') {
            return null;
        }

        foreach (explode(',', $linkHeader) as $segment) {
            if (! str_contains($segment, 'rel="next"')) {
                continue;
            }
            if (preg_match('/[?&]page_info=([^&>]+)/', $segment, $m)) {
                return urldecode($m[1]);
            }
        }

        return null;
    }

    /**
     * Conta pedidos (GET /orders/count). Aceita os mesmos filtros de `list()`.
     *
     * @param  array<string, mixed>  $params
     */
    public function count(array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/orders/count', $params);
    }

    /**
     * Cria um pedido (POST /orders). Embrulha em `order`.
     *
     * @param  array<string, mixed>  $order
     */
    public function create(array $order): array
    {
        return $this->makeRequest(HttpMethod::POST, '/orders', [], ['order' => $order]);
    }

    /**
     * Atualiza um pedido (PUT /orders/{id}). Embrulha em `order`.
     *
     * @param  array<string, mixed>  $order
     */
    public function update(int|string $orderId, array $order): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/orders/{$orderId}", [], ['order' => $order]);
    }

    /**
     * Exclui um pedido (DELETE /orders/{id}). So' pedidos fechados/cancelados
     * ou de teste; pedidos com fulfillment nao podem ser excluidos.
     */
    public function delete(int|string $orderId): array
    {
        return $this->makeRequest(HttpMethod::DELETE, "/orders/{$orderId}");
    }
}
