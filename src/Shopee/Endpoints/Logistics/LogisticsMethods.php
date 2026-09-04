<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\Endpoints\Logistics;

use SistemAtc\Marketplaces\Shopee\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopee\DTO\Response\Logistics\ShippingDocumentParameterResult;
use SistemAtc\Marketplaces\Shopee\DTO\Response\Logistics\ShippingDocumentResult;
use SistemAtc\Marketplaces\Shopee\Exceptions\ShopeeRequestException;

class LogisticsMethods extends BaseMethods
{
    /** @return list<ShippingDocumentParameterResult> */
    public function getShippingDocumentParameter(array $orderSnList): array
    {
        $body = ['order_list' => array_map(fn (string $sn) => ['order_sn' => $sn], array_values($orderSnList))];
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/logistics/get_shipping_document_parameter', [], $body);

        return array_map(
            static fn (array $r): ShippingDocumentParameterResult => ShippingDocumentParameterResult::fromArray($r),
            $response['response']['result_list'] ?? [],
        );
    }

    public function createShippingDocument(array $orderList, string $documentType = 'NORMAL_AIR_WAYBILL'): array
    {
        $orderList = array_map(function (array $o) use ($documentType) {
            $o['shipping_document_type'] ??= $documentType;
            return $o;
        }, array_values($orderList));
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/logistics/create_shipping_document', [], ['order_list' => $orderList]);
        return $response['response']['result_list'] ?? [];
    }

    /** @return list<ShippingDocumentResult> */
    public function getShippingDocumentResult(array $orderList): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/logistics/get_shipping_document_result', [], ['order_list' => array_values($orderList)]);

        return array_map(
            static fn (array $r): ShippingDocumentResult => ShippingDocumentResult::fromArray($r),
            $response['response']['result_list'] ?? [],
        );
    }

    public function downloadShippingDocument(array $orderList, string $documentType = 'NORMAL_AIR_WAYBILL'): string
    {
        $orderList = array_map(function (array $o) use ($documentType) {
            $o['shipping_document_type'] ??= $documentType;
            return $o;
        }, array_values($orderList));

        $apiPath = $this->normalizeApiPath('/api/v2/logistics/download_shipping_document');
        $authQuery = $this->buildAuthQuery($apiPath, false);
        $response = $this->httpClient->post($apiPath.'?'.http_build_query($authQuery), ['order_list' => $orderList]);
        if (!$response->successful()) throw new ShopeeRequestException($response);
        return $response->body();
    }

    // -----------------------------------------------------------------------
    // Envio (ship) por pedido / pacote
    // -----------------------------------------------------------------------

    /**
     * Opcoes de envio de UM pacote: info_needed (pickup/dropoff/non_integrated),
     * enderecos, janelas de coleta e branches. Chame antes do shipOrder().
     * Devolve response.info_needed + response.pickup + response.dropoff.
     */
    public function getShippingParameter(string $orderSn, ?string $packageNumber = null): array
    {
        $query = ['order_sn' => $orderSn];
        if ($packageNumber !== null && $packageNumber !== '') $query['package_number'] = $packageNumber;

        return $this->makeRequest(HttpMethod::GET, '/api/v2/logistics/get_shipping_parameter', $query);
    }

    /**
     * Linha do tempo logistica do pacote (response.tracking_info[] com
     * update_time, description, logistics_status). Sem package_number a
     * Shopee usa o pacote unico do pedido.
     */
    public function getTrackingInfo(string $orderSn, ?string $packageNumber = null): array
    {
        $query = ['order_sn' => $orderSn];
        if ($packageNumber !== null && $packageNumber !== '') $query['package_number'] = $packageNumber;

        return $this->makeRequest(HttpMethod::GET, '/api/v2/logistics/get_tracking_info', $query);
    }

    /**
     * Codigo de rastreio do pacote (response.tracking_number). Optional fields
     * suportados: plp_number, first_mile_tracking_number, last_mile_tracking_number.
     * NAO mande package_number vazio: a Shopee rejeita string vazia.
     *
     * @param list<string>|null $optionalFields
     */
    public function getTrackingNumber(string $orderSn, ?string $packageNumber = null, ?array $optionalFields = null): array
    {
        $query = ['order_sn' => $orderSn];
        if ($packageNumber !== null && $packageNumber !== '') $query['package_number'] = $packageNumber;
        if ($optionalFields) $query['response_optional_fields'] = implode(',', $optionalFields);

        return $this->makeRequest(HttpMethod::GET, '/api/v2/logistics/get_tracking_number', $query);
    }

    /**
     * Despacha (arrange shipment) um pacote. Passe exatamente UM dos blocos
     * que getShippingParameter() pediu em info_needed: pickup
     * (address_id + pickup_time_id), dropoff (branch_id/slug...) ou
     * non_integrated (tracking_number). Bloco nao usado e omitido do body.
     *
     * @param array<string,mixed>|null $pickup
     * @param array<string,mixed>|null $dropoff
     * @param array<string,mixed>|null $nonIntegrated
     */
    public function shipOrder(
        string $orderSn,
        ?string $packageNumber = null,
        ?array $pickup = null,
        ?array $dropoff = null,
        ?array $nonIntegrated = null,
    ): array {
        $body = ['order_sn' => $orderSn];
        if ($packageNumber !== null && $packageNumber !== '') $body['package_number'] = $packageNumber;
        if ($pickup !== null) $body['pickup'] = $pickup;
        if ($dropoff !== null) $body['dropoff'] = $dropoff;
        if ($nonIntegrated !== null) $body['non_integrated'] = $nonIntegrated;

        return $this->makeRequest(HttpMethod::POST, '/api/v2/logistics/ship_order', [], $body);
    }

    /**
     * Despacho em lote (ate 50 pacotes, MESMO metodo pickup/dropoff pra todos).
     * Devolve response.result_list[] com sucesso/erro por pacote.
     *
     * @param list<array{order_sn:string,package_number?:string}> $orderList
     * @param array<string,mixed>|null $pickup
     * @param array<string,mixed>|null $dropoff
     * @param array<string,mixed>|null $nonIntegrated
     */
    public function batchShipOrder(array $orderList, ?array $pickup = null, ?array $dropoff = null, ?array $nonIntegrated = null): array
    {
        $body = ['order_list' => array_values($orderList)];
        if ($pickup !== null) $body['pickup'] = $pickup;
        if ($dropoff !== null) $body['dropoff'] = $dropoff;
        if ($nonIntegrated !== null) $body['non_integrated'] = $nonIntegrated;

        return $this->makeRequest(HttpMethod::POST, '/api/v2/logistics/batch_ship_order', [], $body);
    }

    /**
     * Troca a janela/endereco de coleta de um pacote ja despachado por pickup.
     *
     * @param array{address_id:int,pickup_time_id?:string} $pickup
     */
    public function updateShippingOrder(string $orderSn, array $pickup, ?string $packageNumber = null): array
    {
        $body = ['order_sn' => $orderSn, 'pickup' => $pickup];
        if ($packageNumber !== null && $packageNumber !== '') $body['package_number'] = $packageNumber;

        return $this->makeRequest(HttpMethod::POST, '/api/v2/logistics/update_shipping_order', [], $body);
    }

    /**
     * Seller informa status de canal NAO integrado: logistics_pickup_done,
     * logistics_delivery_done ou logistics_delivery_failed. tracking_number
     * e tracking_url so valem em pickup_done; failed_reason e obrigatorio
     * em delivery_failed (canal BR Instant Delivery 90026).
     */
    public function updateTrackingStatus(
        string $orderSn,
        string $logisticsStatus,
        ?string $trackingNumber = null,
        ?string $trackingUrl = null,
        ?string $failedReason = null,
    ): array {
        $body = ['order_sn' => $orderSn, 'logistics_status' => $logisticsStatus];
        if ($trackingNumber !== null) $body['tracking_number'] = $trackingNumber;
        if ($trackingUrl !== null) $body['tracking_url'] = $trackingUrl;
        if ($failedReason !== null) $body['failed_reason'] = $failedReason;

        return $this->makeRequest(HttpMethod::POST, '/api/v2/logistics/update_tracking_status', [], $body);
    }

    /**
     * Status de armazem 3PF (fulfillment terceirizado) em lote. Statuses em
     * minusculo (3pf_warehouse_order_created, 3pf_warehouse_outbound_done...).
     *
     * @param list<array{order_sn:string,package_number?:string,update_time:int}> $packageList
     */
    public function batchUpdateTpfWarehouseTrackingStatus(string $tpfName, string $tpfTrackingStatus, array $packageList): array
    {
        return $this->makeRequest(HttpMethod::POST, '/api/v2/logistics/batch_update_tpf_warehouse_tracking_status', [], [
            'tpf_name' => $tpfName,
            'tpf_tracking_status' => $tpfTrackingStatus,
            'package_list' => array_values($packageList),
        ]);
    }

    /**
     * Acao de retirada na loja (self collection): ready_for_collection ou
     * order_collected (este exige epoc_image_list, ate 3 image_id).
     *
     * @param list<string>|null $epocImageList
     */
    public function updateSelfCollectionOrderLogistics(
        string $packageNumber,
        string $action,
        ?array $epocImageList = null,
        ?string $pin = null,
    ): array {
        $body = ['package_number' => $packageNumber, 'self_collection_logistics_action' => $action];
        if ($epocImageList !== null) $body['epoc_image_list'] = array_values($epocImageList);
        if ($pin !== null) $body['pin'] = $pin;

        return $this->makeRequest(HttpMethod::POST, '/api/v2/logistics/update_self_collection_order_logistics', [], $body);
    }

    // -----------------------------------------------------------------------
    // Envio em massa (mass ship) — mesmo canal + mesmo product_location_id
    // -----------------------------------------------------------------------

    /**
     * Parametros de envio pra ate 50 pacotes de uma vez. Todos precisam ser
     * do MESMO logistics_channel_id e product_location_id (senao erro).
     *
     * @param list<string> $packageNumbers
     */
    public function getMassShippingParameter(array $packageNumbers, ?int $logisticsChannelId = null, ?string $productLocationId = null): array
    {
        $body = ['package_list' => $this->packageList($packageNumbers)];
        if ($logisticsChannelId !== null) $body['logistics_channel_id'] = $logisticsChannelId;
        if ($productLocationId !== null) $body['product_location_id'] = $productLocationId;

        return $this->makeRequest(HttpMethod::POST, '/api/v2/logistics/get_mass_shipping_parameter', [], $body);
    }

    /**
     * Despacho em massa (ate 50 pacotes). non_integrated aqui e uma LISTA
     * de {package_number, tracking_number} (diferente do ship_order).
     *
     * @param list<string> $packageNumbers
     * @param array<string,mixed>|null $pickup
     * @param array<string,mixed>|null $dropoff
     * @param array<string,mixed>|null $nonIntegrated
     */
    public function massShipOrder(
        array $packageNumbers,
        ?int $logisticsChannelId = null,
        ?string $productLocationId = null,
        ?array $pickup = null,
        ?array $dropoff = null,
        ?array $nonIntegrated = null,
    ): array {
        $body = ['package_list' => $this->packageList($packageNumbers)];
        if ($logisticsChannelId !== null) $body['logistics_channel_id'] = $logisticsChannelId;
        if ($productLocationId !== null) $body['product_location_id'] = $productLocationId;
        if ($pickup !== null) $body['pickup'] = $pickup;
        if ($dropoff !== null) $body['dropoff'] = $dropoff;
        if ($nonIntegrated !== null) $body['non_integrated'] = $nonIntegrated;

        return $this->makeRequest(HttpMethod::POST, '/api/v2/logistics/mass_ship_order', [], $body);
    }

    /**
     * Rastreio de ate 50 pacotes: response.success_list[] + fail_list[].
     *
     * @param list<string> $packageNumbers
     * @param list<string>|null $optionalFields
     */
    public function getMassTrackingNumber(array $packageNumbers, ?array $optionalFields = null): array
    {
        $body = ['package_list' => $this->packageList($packageNumbers)];
        if ($optionalFields) $body['response_optional_fields'] = implode(',', $optionalFields);

        return $this->makeRequest(HttpMethod::POST, '/api/v2/logistics/get_mass_tracking_number', [], $body);
    }

    // -----------------------------------------------------------------------
    // Documento de envio — dados pra AWB proprio e jobs
    // -----------------------------------------------------------------------

    /**
     * Dados da etiqueta pra impressao propria (shipping_document_info +
     * recipient_address_info). Campos de PII podem vir como IMAGEM base64
     * se pedidos em recipientAddressInfo (key + style).
     *
     * @param list<array<string,mixed>>|null $recipientAddressInfo
     */
    public function getShippingDocumentDataInfo(string $orderSn, ?string $packageNumber = null, ?array $recipientAddressInfo = null): array
    {
        $body = ['order_sn' => $orderSn];
        if ($packageNumber !== null && $packageNumber !== '') $body['package_number'] = $packageNumber;
        if ($recipientAddressInfo !== null) $body['recipient_address_info'] = array_values($recipientAddressInfo);

        return $this->makeRequest(HttpMethod::POST, '/api/v2/logistics/get_shipping_document_data_info', [], $body);
    }

    /**
     * Cria job assincrono de etiquetas THERMAL_UNPACKAGED_LABEL. Passe OU
     * packageList (ate 600 package_number) OU unpackagedSkuRequests — nunca
     * os dois. Devolve response.job_id; acompanhe em getShippingDocumentJobStatus().
     *
     * @param list<string>|null $packageList
     * @param list<array{unpackaged_sku_id:string,quantity:int}>|null $unpackagedSkuRequests
     */
    public function createShippingDocumentJob(
        string $shippingDocumentType = 'THERMAL_UNPACKAGED_LABEL',
        ?array $packageList = null,
        ?array $unpackagedSkuRequests = null,
    ): array {
        $body = ['shipping_document_type' => $shippingDocumentType];
        if ($packageList !== null) $body['package_list'] = array_values($packageList);
        if ($unpackagedSkuRequests !== null) $body['unpackaged_sku_requests'] = array_values($unpackagedSkuRequests);

        return $this->makeRequest(HttpMethod::POST, '/api/v2/logistics/create_shipping_document_job', [], $body);
    }

    /** Status do job (response.job_status: PROCESSING|READY|FAILED...). */
    public function getShippingDocumentJobStatus(string $jobId): array
    {
        return $this->makeRequest(HttpMethod::POST, '/api/v2/logistics/get_shipping_document_job_status', [], ['job_id' => $jobId]);
    }

    /**
     * Baixa o PDF gerado pelo job (binario). So chame com job READY — antes
     * disso a Shopee responde JSON de erro, que vira ShopeeRequestException.
     */
    public function downloadShippingDocumentJob(string $jobId): string
    {
        return $this->downloadBinary('/api/v2/logistics/download_shipping_document_job', ['job_id' => $jobId]);
    }

    /**
     * Etiqueta TO (carton) pra drop-off no armazem — SO canal TW 30029.
     * sorting_group 1=North, 2=South; quantity ate 20. Binario (PDF).
     */
    public function downloadToLabel(int $sortingGroup, int $quantity = 1): string
    {
        return $this->downloadBinary('/api/v2/logistics/download_to_label', [
            'sorting_group' => $sortingGroup,
            'quantity' => min(max($quantity, 1), 20),
        ]);
    }

    // -----------------------------------------------------------------------
    // Booking (pedidos "booking_sn" — agendamento de envio, ex.: Shopee Xpress)
    // -----------------------------------------------------------------------

    /** Equivalente do getShippingParameter() pra booking. */
    public function getBookingShippingParameter(string $bookingSn): array
    {
        return $this->makeRequest(HttpMethod::GET, '/api/v2/logistics/get_booking_shipping_parameter', ['booking_sn' => $bookingSn]);
    }

    /**
     * Despacha um booking (pickup OU dropoff conforme info_needed).
     *
     * @param array<string,mixed>|null $pickup
     * @param array<string,mixed>|null $dropoff
     */
    public function shipBooking(string $bookingSn, ?array $pickup = null, ?array $dropoff = null): array
    {
        $body = ['booking_sn' => $bookingSn];
        if ($pickup !== null) $body['pickup'] = $pickup;
        if ($dropoff !== null) $body['dropoff'] = $dropoff;

        return $this->makeRequest(HttpMethod::POST, '/api/v2/logistics/ship_booking', [], $body);
    }

    /** response.tracking_number do booking. */
    public function getBookingTrackingNumber(string $bookingSn): array
    {
        return $this->makeRequest(HttpMethod::GET, '/api/v2/logistics/get_booking_tracking_number', ['booking_sn' => $bookingSn]);
    }

    /** Linha do tempo logistica do booking (response.tracking_info[]). */
    public function getBookingTrackingInfo(string $bookingSn): array
    {
        return $this->makeRequest(HttpMethod::GET, '/api/v2/logistics/get_booking_tracking_info', ['booking_sn' => $bookingSn]);
    }

    /**
     * Tipos de documento disponiveis por booking (ate 50). Devolve
     * response.result_list[].
     *
     * @param list<string> $bookingSnList
     */
    public function getBookingShippingDocumentParameter(array $bookingSnList): array
    {
        return $this->makeRequest(HttpMethod::POST, '/api/v2/logistics/get_booking_shipping_document_parameter', [], [
            'booking_list' => $this->bookingList($bookingSnList),
        ]);
    }

    /**
     * Gera o documento de envio dos bookings (ate 50). Cada item pode trazer
     * tracking_number e shipping_document_type (default aplicado aqui).
     *
     * @param list<array{booking_sn:string,tracking_number?:string,shipping_document_type?:string}> $bookingList
     */
    public function createBookingShippingDocument(array $bookingList, string $documentType = 'NORMAL_AIR_WAYBILL'): array
    {
        $bookingList = array_map(function (array $b) use ($documentType) {
            $b['shipping_document_type'] ??= $documentType;
            return $b;
        }, array_values($bookingList));

        return $this->makeRequest(HttpMethod::POST, '/api/v2/logistics/create_booking_shipping_document', [], ['booking_list' => $bookingList]);
    }

    /**
     * Status da geracao (response.result_list[].status READY|PROCESSING|FAILED).
     *
     * @param list<array{booking_sn:string,shipping_document_type?:string}> $bookingList
     */
    public function getBookingShippingDocumentResult(array $bookingList): array
    {
        return $this->makeRequest(HttpMethod::POST, '/api/v2/logistics/get_booking_shipping_document_result', [], [
            'booking_list' => array_values($bookingList),
        ]);
    }

    /**
     * PDF das etiquetas dos bookings (binario, ate 50 por chamada).
     *
     * @param list<string> $bookingSnList
     */
    public function downloadBookingShippingDocument(array $bookingSnList, string $documentType = 'NORMAL_AIR_WAYBILL'): string
    {
        return $this->downloadBinary('/api/v2/logistics/download_booking_shipping_document', [
            'shipping_document_type' => $documentType,
            'booking_list' => $this->bookingList($bookingSnList),
        ]);
    }

    /**
     * Dados da etiqueta do booking pra impressao propria (PII como imagem
     * opcional via recipientAddressInfo).
     *
     * @param list<array<string,mixed>>|null $recipientAddressInfo
     */
    public function getBookingShippingDocumentDataInfo(string $bookingSn, ?array $recipientAddressInfo = null): array
    {
        $body = ['booking_sn' => $bookingSn];
        if ($recipientAddressInfo !== null) $body['recipient_address_info'] = array_values($recipientAddressInfo);

        return $this->makeRequest(HttpMethod::POST, '/api/v2/logistics/get_booking_shipping_document_data_info', [], $body);
    }

    // -----------------------------------------------------------------------
    // Canais e enderecos da loja
    // -----------------------------------------------------------------------

    /** Canais logisticos da loja (response.logistics_channel_list[]). */
    public function getChannelList(): array
    {
        return $this->makeRequest(HttpMethod::GET, '/api/v2/logistics/get_channel_list');
    }

    /**
     * Liga/desliga canal, COD e auto-chamada de motorista.
     *
     * @param array{auto_call_driver_enabled?:bool,preparation_time?:int}|null $autoCallDriverSetting
     */
    public function updateChannel(int $logisticsChannelId, ?bool $enabled = null, ?bool $codEnabled = null, ?array $autoCallDriverSetting = null): array
    {
        $body = ['logistics_channel_id' => $logisticsChannelId];
        if ($enabled !== null) $body['enabled'] = $enabled;
        if ($codEnabled !== null) $body['cod_enabled'] = $codEnabled;
        if ($autoCallDriverSetting !== null) $body['auto_call_driver_setting'] = $autoCallDriverSetting;

        return $this->makeRequest(HttpMethod::POST, '/api/v2/logistics/update_channel', [], $body);
    }

    /** Enderecos da loja (response.address_list[] com address_id e address_type[]). */
    public function getAddressList(): array
    {
        return $this->makeRequest(HttpMethod::GET, '/api/v2/logistics/get_address_list');
    }

    /**
     * Atualiza um endereco existente (address_id de getAddressList()). Region
     * NAO pode mudar. geo_info: JSON string; "" ou {} limpa, omitir mantem.
     *
     * @param array<string,mixed> $fields
     */
    public function updateAddress(int $addressId, array $fields): array
    {
        return $this->makeRequest(HttpMethod::POST, '/api/v2/logistics/update_address', [], ['address_id' => $addressId] + $fields);
    }

    /** Remove endereco da loja. */
    public function deleteAddress(int $addressId): array
    {
        return $this->makeRequest(HttpMethod::POST, '/api/v2/logistics/delete_address', [], ['address_id' => $addressId]);
    }

    /**
     * Define papel dos enderecos (pickup/return/default) e se mostra o
     * endereco de coleta.
     *
     * @param array{address_id:int,address_type:list<string>}|null $addressTypeConfig
     */
    public function setAddressConfig(?bool $showPickupAddress = null, ?array $addressTypeConfig = null): array
    {
        $body = [];
        if ($showPickupAddress !== null) $body['show_pickup_address'] = $showPickupAddress;
        if ($addressTypeConfig !== null) $body['address_type_config'] = $addressTypeConfig;

        return $this->makeRequest(HttpMethod::POST, '/api/v2/logistics/set_address_config', [], $body);
    }

    // -----------------------------------------------------------------------
    // Horario de operacao / pausa / embalagem / poligono (Instant Delivery)
    // -----------------------------------------------------------------------

    /** Restricoes (janela minima etc.) que a loja precisa respeitar ao editar horarios. */
    public function getOperatingHourRestrictions(): array
    {
        return $this->makeRequest(HttpMethod::GET, '/api/v2/logistics/get_operating_hour_restrictions');
    }

    /** Horarios atuais: regular, special, instant e shop_collection. */
    public function getOperatingHours(): array
    {
        return $this->makeRequest(HttpMethod::GET, '/api/v2/logistics/get_operating_hours');
    }

    /**
     * Atualiza horarios. Blocos omitidos NAO sao alterados. Cada bloco segue
     * o formato de getOperatingHours() (monday..sunday + public_holiday com
     * start_time/end_time "HH:MM"). ATENCAO: no instant_operating_hour a
     * chave de quinta e "thrusday" (typo oficial da Shopee).
     *
     * @param array<string,mixed>|null $regularOperatingHour
     * @param array<string,mixed>|null $specialOperatingHour
     * @param array<string,mixed>|null $instantOperatingHour
     * @param array<string,mixed>|null $shopCollectionOperatingHour
     */
    public function updateOperatingHours(
        ?array $regularOperatingHour = null,
        ?array $specialOperatingHour = null,
        ?array $instantOperatingHour = null,
        ?array $shopCollectionOperatingHour = null,
    ): array {
        $body = [];
        if ($regularOperatingHour !== null) $body['regular_operating_hour'] = $regularOperatingHour;
        if ($specialOperatingHour !== null) $body['special_operating_hour'] = $specialOperatingHour;
        if ($instantOperatingHour !== null) $body['instant_operating_hour'] = $instantOperatingHour;
        if ($shopCollectionOperatingHour !== null) $body['shop_collection_operating_hour'] = $shopCollectionOperatingHour;

        return $this->makeRequest(HttpMethod::POST, '/api/v2/logistics/update_operating_hours', [], $body);
    }

    /** Apaga um horario especial pelo name (de getOperatingHours()). */
    public function deleteSpecialOperatingHour(string $name): array
    {
        return $this->makeRequest(HttpMethod::POST, '/api/v2/logistics/delete_special_operating_hour', [], ['name' => $name]);
    }

    /** Se a loja esta pausada pra pedidos instant (response.is_paused). */
    public function getPauseStatus(): array
    {
        return $this->makeRequest(HttpMethod::GET, '/api/v2/logistics/get_pause_status');
    }

    /** Pausa/retoma os canais instant da loja. */
    public function setPauseStatus(bool $isPaused): array
    {
        return $this->makeRequest(HttpMethod::POST, '/api/v2/logistics/set_pause_status', [], ['is_paused' => $isPaused]);
    }

    /** Config de taxa de embalagem (mart). */
    public function getMartPackagingInfo(): array
    {
        return $this->makeRequest(HttpMethod::GET, '/api/v2/logistics/get_mart_packaging_info');
    }

    /**
     * Liga/desliga taxa de embalagem. Com enable=true, dimension
     * (length/width/height) e packaging_fee (value) sao obrigatorios.
     *
     * @param array{length:int,width:int,height:int}|null $dimension
     * @param array{value:float}|null $packagingFee
     */
    public function setMartPackagingInfo(bool $enable, ?array $dimension = null, ?array $packagingFee = null): array
    {
        $body = ['enable' => $enable];
        if ($dimension !== null) $body['dimension'] = $dimension;
        if ($packagingFee !== null) $body['packaging_fee'] = $packagingFee;

        return $this->makeRequest(HttpMethod::POST, '/api/v2/logistics/set_mart_packaging_info', [], $body);
    }

    /**
     * Sobe o .kml da area de atendimento (multipart). Assincrono: devolve
     * response.task_id; acompanhe em checkPolygonUpdateStatus().
     */
    public function uploadServiceablePolygon(string $kmlContents, string $filename = 'polygon.kml'): array
    {
        $apiPath = $this->normalizeApiPath('/api/v2/logistics/upload_serviceable_polygon');
        $authQuery = $this->buildAuthQuery($apiPath, false);

        $client = $this->multipartClient()->attach('file', $kmlContents, $filename);
        $response = $client->post($apiPath.'?'.http_build_query($authQuery));

        $data = $response->json() ?? [];
        if ($response->failed() || ! empty($data['error'])) throw new ShopeeRequestException($response);

        return $data;
    }

    /** Status do processamento do poligono (task_id de uploadServiceablePolygon()). */
    public function checkPolygonUpdateStatus(string $taskId): array
    {
        return $this->makeRequest(HttpMethod::POST, '/api/v2/logistics/check_polygon_update_status', [], ['task_id' => $taskId]);
    }

    // -----------------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------------

    /**
     * POST autenticado que devolve BINARIO (PDF). A Shopee responde erro de
     * negocio como JSON com HTTP 200 — detectado pelo content-type.
     *
     * @param array<string,mixed> $body
     */
    private function downloadBinary(string $path, array $body): string
    {
        $apiPath = $this->normalizeApiPath($path);
        $authQuery = $this->buildAuthQuery($apiPath, false);
        $response = $this->httpClient->post($apiPath.'?'.http_build_query($authQuery), $body);

        if (! $response->successful()) throw new ShopeeRequestException($response);
        if (str_contains((string) $response->header('Content-Type'), 'json')) {
            $data = $response->json() ?? [];
            if (! empty($data['error'])) throw new ShopeeRequestException($response);
        }

        return $response->body();
    }

    /** @param list<string> $packageNumbers @return list<array{package_number:string}> */
    private function packageList(array $packageNumbers): array
    {
        return array_map(static fn (string $p): array => ['package_number' => $p], array_values($packageNumbers));
    }

    /** @param list<string> $bookingSnList @return list<array{booking_sn:string}> */
    private function bookingList(array $bookingSnList): array
    {
        return array_map(static fn (string $b): array => ['booking_sn' => $b], array_values($bookingSnList));
    }

}
