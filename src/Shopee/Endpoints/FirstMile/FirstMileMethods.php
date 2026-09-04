<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\Endpoints\FirstMile;

use SistemAtc\Marketplaces\Shopee\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopee\Exceptions\ShopeeRequestException;

/**
 * First Mile — consolidacao de pacotes cross-border (CN/KR) num unico
 * first_mile_tracking_number (FM TN) ou binding_id (courier_delivery).
 * Sem uso pra loja BR local hoje; implementado por cobertura de catalogo.
 * Datas no formato "YYYY-MM-DD"; listas limitadas a 50 pedidos por chamada.
 */
class FirstMileMethods extends BaseMethods
{
    /** Canais first-mile disponiveis (response.logistics_channel_list[]). region CN|KR. */
    public function getChannelList(?string $region = null): array
    {
        $query = [];
        if ($region !== null) $query['region'] = $region;

        return $this->makeRequest(HttpMethod::GET, '/api/v2/first_mile/get_channel_list', $query);
    }

    /** Canais de courier_delivery (region CN). */
    public function getCourierDeliveryChannelList(?string $region = null): array
    {
        $query = [];
        if ($region !== null) $query['region'] = $region;

        return $this->makeRequest(HttpMethod::GET, '/api/v2/first_mile/get_courier_delivery_channel_list', $query);
    }

    /** Armazens de transito (warehouse_id + warehouse_type) por metodo de envio. */
    public function getTransitWarehouseList(?string $region = null, ?string $shipmentMethod = null): array
    {
        $query = [];
        if ($region !== null) $query['region'] = $region;
        if ($shipmentMethod !== null) $query['shipment_method'] = $shipmentMethod;

        return $this->makeRequest(HttpMethod::GET, '/api/v2/first_mile/get_transit_warehouse_list', $query);
    }

    /**
     * Gera FM TNs pra um dia de declaracao (ate 20 por dia).
     * declare_date "YYYY-MM-DD". Devolve response.first_mile_tracking_number_list[].
     */
    public function generateFirstMileTrackingNumber(string $declareDate, int $quantity = 1): array
    {
        return $this->makeRequest(HttpMethod::POST, '/api/v2/first_mile/generate_first_mile_tracking_number', [], [
            'declare_date' => $declareDate,
            'quantity' => min(max($quantity, 1), 20),
        ]);
    }

    /**
     * Vincula ate 50 pacotes a um FM TN. shipment_method pickup|dropoff|
     * self_deliver; region cn|kr; dimensoes opcionais. Em dropoff informe
     * warehouse_id/warehouse_type de getTransitWarehouseList().
     *
     * @param list<array{order_sn:string,package_number?:string}> $orderList
     * @param array<string,mixed> $extra volume, weight, width, length, height, warehouse_id, warehouse_type
     */
    public function bindFirstMileTrackingNumber(
        string $firstMileTrackingNumber,
        string $shipmentMethod,
        string $region,
        int $logisticsChannelId,
        array $orderList,
        array $extra = [],
    ): array {
        $body = [
            'first_mile_tracking_number' => $firstMileTrackingNumber,
            'shipment_method' => $shipmentMethod,
            'region' => $region,
            'logistics_channel_id' => $logisticsChannelId,
            'order_list' => array_values($orderList),
        ] + $extra;

        return $this->makeRequest(HttpMethod::POST, '/api/v2/first_mile/bind_first_mile_tracking_number', [], $body);
    }

    /**
     * Desvincula pedidos de um FM TN (ate 50).
     *
     * @param list<array{order_sn:string,package_number?:string}> $orderList
     */
    public function unbindFirstMileTrackingNumber(string $firstMileTrackingNumber, array $orderList): array
    {
        return $this->makeRequest(HttpMethod::POST, '/api/v2/first_mile/unbind_first_mile_tracking_number', [], [
            'first_mile_tracking_number' => $firstMileTrackingNumber,
            'order_list' => array_values($orderList),
        ]);
    }

    /**
     * Desvincula pedidos de QUALQUER FM TN / binding_id (ate 50), sem
     * precisar saber o numero atual.
     *
     * @param list<array{order_sn:string,package_number?:string}> $orderList
     */
    public function unbindFirstMileTrackingNumberAll(array $orderList): array
    {
        return $this->makeRequest(HttpMethod::POST, '/api/v2/first_mile/unbind_first_mile_tracking_number_all', [], [
            'order_list' => array_values($orderList),
        ]);
    }

    /** Detalhe de um FM TN (pedidos vinculados, paginado por cursor). */
    public function getDetail(string $firstMileTrackingNumber, string $cursor = ''): array
    {
        $query = ['first_mile_tracking_number' => $firstMileTrackingNumber];
        if ($cursor !== '') $query['cursor'] = $cursor;

        return $this->makeRequest(HttpMethod::GET, '/api/v2/first_mile/get_detail', $query);
    }

    /**
     * FM TNs por janela de declare_date ("YYYY-MM-DD"). Paginado por cursor
     * (response.more + response.next_cursor).
     */
    public function getTrackingNumberList(string $fromDate, string $toDate, int $pageSize = 50, string $cursor = ''): array
    {
        $query = ['from_date' => $fromDate, 'to_date' => $toDate, 'page_size' => $pageSize];
        if ($cursor !== '') $query['cursor'] = $cursor;

        return $this->makeRequest(HttpMethod::GET, '/api/v2/first_mile/get_tracking_number_list', $query);
    }

    /**
     * Pedidos ainda sem FM TN (response.order_list[]), paginado por cursor.
     *
     * @param list<string>|null $optionalFields ex.: logistics_status, package_number
     */
    public function getUnbindOrderList(int $pageSize = 50, string $cursor = '', ?array $optionalFields = null): array
    {
        $query = ['page_size' => $pageSize];
        if ($cursor !== '') $query['cursor'] = $cursor;
        if ($optionalFields) $query['response_optional_fields'] = implode(',', $optionalFields);

        return $this->makeRequest(HttpMethod::GET, '/api/v2/first_mile/get_unbind_order_list', $query);
    }

    /**
     * Waybill (PDF binario) de ate 50 FM TNs. Erro de negocio vem como JSON
     * mesmo com HTTP 200 -> ShopeeRequestException.
     *
     * @param list<string> $firstMileTrackingNumbers
     */
    public function getWaybill(array $firstMileTrackingNumbers): string
    {
        $apiPath = $this->normalizeApiPath('/api/v2/first_mile/get_waybill');
        $authQuery = $this->buildAuthQuery($apiPath, false);
        $response = $this->httpClient->post($apiPath.'?'.http_build_query($authQuery), [
            'first_mile_tracking_number_list' => array_values($firstMileTrackingNumbers),
        ]);

        if (! $response->successful()) throw new ShopeeRequestException($response);
        if (str_contains((string) $response->header('Content-Type'), 'json')) {
            $data = $response->json() ?? [];
            if (! empty($data['error'])) throw new ShopeeRequestException($response);
        }

        return $response->body();
    }

    // -----------------------------------------------------------------------
    // Courier delivery (binding_id em vez de FM TN)
    // -----------------------------------------------------------------------

    /**
     * Gera binding_id e ja vincula ate 50 pedidos (shipment_method
     * courier_delivery). courier_delivery_info: address_id, warehouse_id,
     * logistics_product_id, prepaid_account_id, courier_service_id.
     *
     * @param list<array{order_sn:string,package_number?:string}> $orderList
     * @param array<string,mixed> $courierDeliveryInfo
     */
    public function generateAndBindFirstMileTrackingNumber(
        array $orderList,
        array $courierDeliveryInfo,
        string $shipmentMethod = 'courier_delivery',
        ?string $region = null,
    ): array {
        $body = [
            'shipment_method' => $shipmentMethod,
            'order_list' => array_values($orderList),
            'courier_delivery_info' => $courierDeliveryInfo,
        ];
        if ($region !== null) $body['region'] = $region;

        return $this->makeRequest(HttpMethod::POST, '/api/v2/first_mile/generate_and_bind_first_mile_tracking_number', [], $body);
    }

    /**
     * Vincula mais pedidos a um binding_id existente (ate 50).
     *
     * @param list<array{order_sn:string,package_number?:string}> $orderList
     */
    public function bindCourierDeliveryFirstMileTrackingNumber(string $bindingId, array $orderList, string $shipmentMethod = 'courier_delivery'): array
    {
        return $this->makeRequest(HttpMethod::POST, '/api/v2/first_mile/bind_courier_delivery_first_mile_tracking_number', [], [
            'shipment_method' => $shipmentMethod,
            'binding_id' => $bindingId,
            'order_list' => array_values($orderList),
        ]);
    }

    /** Detalhe de um binding_id (pedidos vinculados, paginado por cursor). */
    public function getCourierDeliveryDetail(string $bindingId, int $pageSize = 50, string $cursor = ''): array
    {
        $query = ['binding_id' => $bindingId, 'page_size' => $pageSize];
        if ($cursor !== '') $query['cursor'] = $cursor;

        return $this->makeRequest(HttpMethod::GET, '/api/v2/first_mile/get_courier_delivery_detail', $query);
    }

    /**
     * URLs das etiquetas por binding_id (ate 50). Diferente do getWaybill(),
     * aqui a resposta e JSON: response.waybill_list[].shipping_label_url.
     *
     * @param list<string> $bindingIds
     */
    public function getCourierDeliveryWaybill(array $bindingIds): array
    {
        return $this->makeRequest(HttpMethod::POST, '/api/v2/first_mile/get_courier_delivery_waybill', [], [
            'binding_id_list' => array_values($bindingIds),
        ]);
    }

    /**
     * binding_ids por janela de criacao ("YYYY-MM-DD"), POST paginado por cursor.
     */
    public function getCourierDeliveryTrackingNumberList(string $fromDate, string $toDate, int $pageSize = 50, string $cursor = ''): array
    {
        $body = ['from_date' => $fromDate, 'to_date' => $toDate, 'page_size' => $pageSize];
        if ($cursor !== '') $body['cursor'] = $cursor;

        return $this->makeRequest(HttpMethod::POST, '/api/v2/first_mile/get_courier_delivery_tracking_number_list', [], $body);
    }
}
