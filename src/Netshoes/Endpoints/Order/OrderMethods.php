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
 *
 * DIVERGENCIA V1 x V2 (documentada, nao resolvida pela lib): o Swagger
 * oficial do portal (netshoes_v1.json, basePath /api/v1) so' descreve pedidos
 * em V1 — nao existe /api/v2/orders no Swagger. Ja' em teste real V1 devolveu
 * 409 "migracao para API V2" e V2 respondeu. Por isso:
 *   - getOrder / getOrders / getAllOrders ficam em /api/v2 (comportamento
 *     historico, intocado);
 *   - os metodos sufixados V1 (listOrdersV1, getOrderV1, saveOrder) e todos
 *     os de shipping/status/tags/cancellation-reasons seguem o Swagger V1
 *     (/api/v1), unica fonte oficial dessas operacoes.
 * A base nao prefixa versao — cada metodo informa o path completo.
 *
 * Header opcional `idSeller` (Swagger: listOrders e PUT status/*) identifica
 * o seller quando a credencial atende mais de um.
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

    // ------------------------------------------------------------------
    // Swagger V1 — /api/v1/orders (Orders + Cancellation Reasons)
    // ------------------------------------------------------------------

    /**
     * Lista paginada de pedidos (Swagger V1 listOrders).
     *
     * @param  array<string, mixed>  $query  page, size, expand, orderStartDate,
     *                                       orderEndDate, orderStatus, orderType
     * @return array<string, mixed>  ListResponseOrderResource {items[], links[]}
     */
    public function listOrdersV1(array $query = [], ?string $idSeller = null): array
    {
        return $this->makeRequest(
            method: HttpMethod::GET,
            path: '/api/v1/orders',
            query: $query,
            headers: $this->sellerHeader($idSeller),
        );
    }

    /**
     * Detalhe de um pedido pelo orderNumber (Swagger V1 getOrder).
     *
     * @param  array<int, string>  $expand
     * @return array<string, mixed>  OrderResource
     */
    public function getOrderV1(string $orderNumber, array $expand = []): array
    {
        return $this->makeRequest(
            method: HttpMethod::GET,
            path: '/api/v1/orders/'.rawurlencode($orderNumber),
            query: $this->expandQuery($expand),
        );
    }

    /**
     * Cria um pedido — SOMENTE SANDBOX (Swagger V1 saveOrder).
     *
     * @param  array<string, mixed>  $order  OrderResource (orderNumber, orderDate,
     *                                       orderStatus, orderType, businessUnit,
     *                                       originSite, totais, shippings[]...)
     * @return array<string, mixed>
     */
    public function saveOrder(array $order): array
    {
        return $this->makeRequest(method: HttpMethod::POST, path: '/api/v1/orders', body: $order);
    }

    /**
     * Entregas (shippings) de um pedido (Swagger V1 listOrderShippings).
     *
     * @param  array<int, string>  $expand
     * @return array<string, mixed>  NoPageableListResponseShippingResource
     */
    public function listShippings(string $orderNumber, array $expand = []): array
    {
        return $this->makeRequest(
            method: HttpMethod::GET,
            path: '/api/v1/orders/'.rawurlencode($orderNumber).'/shippings',
            query: $this->expandQuery($expand),
        );
    }

    /**
     * Uma entrega pelo shippingCode (Swagger V1 getOrderShipping).
     *
     * @param  array<int, string>  $expand
     * @return array<string, mixed>  ShippingResource
     */
    public function getShipping(string $orderNumber, string|int $shippingCode, array $expand = []): array
    {
        return $this->makeRequest(
            method: HttpMethod::GET,
            path: $this->shippingPath($orderNumber, $shippingCode),
            query: $this->expandQuery($expand),
        );
    }

    /**
     * Status -> approved — SOMENTE SANDBOX (Swagger V1 updateShippingStatusToApproved).
     *
     * @param  array<string, mixed>  $body  ApprovedStatusResource {status}
     * @return array<string, mixed>
     */
    public function updateShippingStatusToApproved(
        string $orderNumber,
        string|int $shippingCode,
        array $body = ['status' => 'approved'],
        ?string $idSeller = null,
    ): array {
        return $this->updateShippingStatus($orderNumber, $shippingCode, 'approved', $body, $idSeller);
    }

    /**
     * Status -> canceled (Swagger V1 updateShippingStatusToCanceled). Depois de
     * faturado o pedido NAO cancela. Codigos em getCancellationReasons().
     *
     * @param  array<string, mixed>  $body  CanceledStatusResource
     *                                      {reasonCancellationCode*, canceledBy, status}
     * @return array<string, mixed>
     */
    public function updateShippingStatusToCanceled(
        string $orderNumber,
        string|int $shippingCode,
        array $body,
        ?string $idSeller = null,
    ): array {
        return $this->updateShippingStatus($orderNumber, $shippingCode, 'canceled', $body, $idSeller);
    }

    /**
     * Status -> delivered (Swagger V1 updateShippingStatusToDelivered).
     *
     * @param  array<string, mixed>  $body  DeliveredStatusResource {deliveryDate, status}
     * @return array<string, mixed>
     */
    public function updateShippingStatusToDelivered(
        string $orderNumber,
        string|int $shippingCode,
        array $body,
        ?string $idSeller = null,
    ): array {
        return $this->updateShippingStatus($orderNumber, $shippingCode, 'delivered', $body, $idSeller);
    }

    /**
     * Status -> invoiced (Swagger V1 updateShippingStatusToInvoiced).
     *
     * @param  array<string, mixed>  $body  InvoicedStatusResource
     *                                      {number, line, key, issueDate, volume, danfeXml, status}
     * @return array<string, mixed>
     */
    public function updateShippingStatusToInvoiced(
        string $orderNumber,
        string|int $shippingCode,
        array $body,
        ?string $idSeller = null,
    ): array {
        return $this->updateShippingStatus($orderNumber, $shippingCode, 'invoiced', $body, $idSeller);
    }

    /**
     * Status -> shipped (Swagger V1 updateShippingStatusToShipped).
     *
     * @param  array<string, mixed>  $body  ShippedStatusResource {carrier, trackingNumber,
     *                                      trackingLink, estimatedDelivery, deliveredCarrierDate, status}
     * @return array<string, mixed>
     */
    public function updateShippingStatusToShipped(
        string $orderNumber,
        string|int $shippingCode,
        array $body,
        ?string $idSeller = null,
    ): array {
        return $this->updateShippingStatus($orderNumber, $shippingCode, 'shipped', $body, $idSeller);
    }

    /**
     * Solicita coleta / etiquetas a logistica (Swagger V1 postShippingTags).
     *
     * @param  array<int, string|int>  $shippingCodes
     * @return array<string, mixed>  PickupTrackingGroupResource
     */
    public function createShippingTags(array $shippingCodes, string $documentType = 'PDF'): array
    {
        return $this->makeRequest(
            method: HttpMethod::POST,
            path: '/api/v1/orders/shipping-tags',
            body: ['documentType' => $documentType, 'shippingCodes' => array_values($shippingCodes)],
        );
    }

    /**
     * Motivos de cancelamento aceitos (Swagger V1 getCancellationReasons).
     *
     * @return array<int|string, mixed>  CancellationReasonsResource[] {code, description, canBePenalty}
     */
    public function getCancellationReasons(): array
    {
        return $this->makeRequest(HttpMethod::GET, '/api/v1/orders/cancellation-reasons');
    }

    /**
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    private function updateShippingStatus(
        string $orderNumber,
        string|int $shippingCode,
        string $status,
        array $body,
        ?string $idSeller,
    ): array {
        return $this->makeRequest(
            method: HttpMethod::PUT,
            path: $this->shippingPath($orderNumber, $shippingCode).'/status/'.$status,
            body: $body,
            headers: $this->sellerHeader($idSeller),
        );
    }

    private function shippingPath(string $orderNumber, string|int $shippingCode): string
    {
        return '/api/v1/orders/'.rawurlencode($orderNumber).'/shippings/'.rawurlencode((string) $shippingCode);
    }

    /**
     * @param  array<int, string>  $expand
     * @return array<string, string>
     */
    private function expandQuery(array $expand): array
    {
        return $expand ? ['expand' => implode(',', $expand)] : [];
    }

    /** @return array<string, string> */
    private function sellerHeader(?string $idSeller): array
    {
        return $idSeller !== null && $idSeller !== '' ? ['idSeller' => $idSeller] : [];
    }
}
