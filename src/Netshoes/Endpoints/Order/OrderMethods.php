<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Netshoes\Endpoints\Order;

use SistemAtc\Marketplaces\Netshoes\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;

/**
 * Endpoints de pedidos Netshoes — API V2 (V1 descontinuada: devolve 409
 * "migração para API V2").
 *
 *   - GET /api/v2/orders/{orderNumber}?expand=items,shippings  (detalhe)
 *   - GET /api/v2/orders?page=0&size=50                        (lista paginada)
 *
 * Paginacao por page/size (page base 0, size max 50). Datas em ISO-8601.
 *
 * SCHEMA DO PEDIDO (confirmado pelo Swagger, 2026-06-25): SHIPPING-CENTRICO.
 * Top-level = orderNumber (numero visivel), originNumber, businessUnit
 * (Netshoes|Zattini), orderType, totalGross, shippings[], payment{}, links[].
 * Cada shipping carrega status (minusculo: approved|invoiced|shipped|delivered|
 * canceled), transport{}, invoice{accessKey,date,shipDate}, sender{}, items[],
 * devolutionItems[]. NAO ha objeto customer/buyer (sem PII do comprador).
 *
 * O SDK devolve o JSON CRU da API (sem desnormalizar) — a normalizacao fica
 * no consumidor (Bunker: PullNetshoesOrderByIdJob).
 */
class OrderMethods extends BaseMethods
{
    /** Page size maximo aceito pela API V2. */
    public const MAX_PAGE_SIZE = 50;

    /** Guard anti-runaway: nunca passa disso num unico ciclo de paginacao. */
    private const MAX_PAGES = 1000;

    /**
     * Detalhe de UM pedido pelo numero visivel (orderNumber).
     *
     * @param  array<int, string>  $expand  sub-recursos a expandir (items, shippings)
     * @return array<string, mixed>
     */
    public function getOrder(string $orderNumber, array $expand = ['items', 'shippings']): array
    {
        $query = [];
        if (! empty($expand)) {
            $query['expand'] = implode(',', $expand);
        }

        return $this->makeRequest(HttpMethod::GET, "/api/v2/orders/{$orderNumber}", $query);
    }

    /**
     * UMA pagina de pedidos.
     *
     * @param  array<string, mixed>  $params  page (base 0), size (<=50), e
     *                                        filtros de data ISO-8601 da API
     *                                        (ex startDate/endDate/status)
     * @return array<string, mixed>
     */
    public function getOrders(array $params = []): array
    {
        $query = array_merge([
            'page' => 0,
            'size' => self::MAX_PAGE_SIZE,
        ], $params);

        $query['size'] = min((int) $query['size'], self::MAX_PAGE_SIZE);
        $query['page'] = max((int) $query['page'], 0);

        return $this->makeRequest(HttpMethod::GET, '/api/v2/orders', $query);
    }

    /**
     * Lista paginada automatica — itera page/size ate' esgotar e devolve a
     * lista achatada de pedidos. Rede de seguranca do polling.
     *
     * Tolera os shapes mais comuns de resposta paginada (content/results/
     * orders/data ou a propria raiz como array). Para quando a pagina vem
     * vazia OU quando a contagem retornada e' menor que o size pedido.
     *
     * @param  array<string, mixed>  $params
     * @return array<int, array<string, mixed>>
     */
    public function getAllOrders(array $params = []): array
    {
        $size = min((int) ($params['size'] ?? self::MAX_PAGE_SIZE), self::MAX_PAGE_SIZE);
        $page = max((int) ($params['page'] ?? 0), 0);

        $all = [];

        for ($i = 0; $i < self::MAX_PAGES; $i++) {
            $resp = $this->getOrders(array_merge($params, [
                'page' => $page,
                'size' => $size,
            ]));

            $orders = $this->extractOrders($resp);

            if (empty($orders)) {
                break;
            }

            foreach ($orders as $order) {
                $all[] = $order;
            }

            // Ultima pagina (vieram menos que o size pedido).
            if (count($orders) < $size) {
                break;
            }

            $page++;
        }

        return $all;
    }

    /**
     * Extrai a lista de pedidos de uma resposta paginada, tolerando os
     * envelopes mais comuns. BEST-EFFORT ate' o shape real ser confirmado.
     *
     * @param  array<string, mixed>  $resp
     * @return array<int, array<string, mixed>>
     */
    private function extractOrders(array $resp): array
    {
        foreach (['content', 'results', 'orders', 'data', '_embedded'] as $key) {
            if (isset($resp[$key]) && is_array($resp[$key])) {
                $candidate = $resp[$key];
                // _embedded.orders (HAL) — desce mais um nivel se preciso.
                if ($key === '_embedded' && isset($candidate['orders']) && is_array($candidate['orders'])) {
                    return array_values($candidate['orders']);
                }

                return array_values($candidate);
            }
        }

        // Resposta ja' e' uma lista crua.
        if (array_is_list($resp)) {
            return $resp;
        }

        return [];
    }
}
