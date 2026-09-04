<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\Endpoints\Order;

use SistemAtc\Marketplaces\Shopee\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Shopee\Exceptions\ShopeeRequestException;
use SistemAtc\Marketplaces\Shopee\DTO\Response\Order\FbsDownloadItem;
use SistemAtc\Marketplaces\Shopee\DTO\Response\Order\FbsRequestListResponseDTO;
use SistemAtc\Marketplaces\Shopee\DTO\Response\Order\InvoiceData;
use SistemAtc\Marketplaces\Shopee\DTO\Response\Order\OrderListResponseDTO;
use SistemAtc\Marketplaces\Shopee\DTO\Response\Order\OrderResponseDTO;

class OrderMethods extends BaseMethods
{
    /**
     * Campos pedidos por padrao no get_order_detail.
     *
     * A Shopee so devolve o que esta em `response_optional_fields` — campo
     * fora dessa lista simplesmente NAO vem, e o consumidor recebe null sem
     * nenhum erro.
     *
     * ESTA LISTA E CRITICA. O conector interno antigo (app/Services/Shopee, no
     * ERP) pedia 33 campos; a migracao pra esta lib em 2026-06-08 trocou o
     * default por uma lista de 9 e derrubou `total_amount`, `fulfillment_flag`,
     * `payment_method`, `shipping_carrier` e `package_list` em SILENCIO. Efeito
     * no ERP: 99% dos pedidos Shopee de julho/2026 ficaram sem valor
     * (total_value), sem classificacao Full/FBS (logistic_type) e sem
     * order_payments. Ninguem viu por ~40 dias porque null nao levanta erro.
     *
     * Ao mexer aqui, lembre: TIRAR um campo desta lista APAGA o dado no
     * consumidor, nao so deixa de trazer. Ver tests/Shopee/OrderDetailFieldsTest.
     *
     * edt_from/edt_to/prescription_images/prescription_check_status ficaram de
     * fora: a Shopee BR nunca os devolve (verificado ao vivo em 2026-07-17).
     */
    private const DETAIL_FIELDS = [
        'order_status',
        'total_amount',
        'fulfillment_flag',
        'payment_method',
        'shipping_carrier',
        'package_list',
        'item_list',
        'invoice_data',
        'recipient_address',
        'buyer_user_id',
        'buyer_username',
        'buyer_cpf_id',
        'pay_time',
        'actual_shipping_fee',
        'actual_shipping_fee_confirmed',
        'estimated_shipping_fee',
        'goods_to_declare',
        'note',
        'note_update_time',
        'dropshipper',
        'dropshipper_phone',
        'split_up',
        'buyer_cancel_reason',
        'cancel_by',
        'cancel_reason',
        'pickup_done_time',
        'order_chargeable_weight_gram',
        'booking_sn',
        'advance_package',
        'return_request_due_date',
    ];

    public function getOrderList(
        int $timeFrom,
        int $timeTo,
        string $timeRangeField = 'create_time',
        ?string $orderStatus = null,
        int $pageSize = 50,
        string $cursor = '',
    ): OrderListResponseDTO {
        $query = [
            'time_range_field' => $timeRangeField,
            'time_from' => $timeFrom,
            'time_to' => $timeTo,
            'page_size' => min($pageSize, 100),
            'cursor' => $cursor,
        ];

        if ($orderStatus !== null) $query['order_status'] = $orderStatus;

        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/order/get_order_list', $query);

        return OrderListResponseDTO::fromArray([
            'order_list' => $response['response']['order_list'] ?? [],
            'more' => $response['response']['more'] ?? false,
            'next_cursor' => $response['response']['next_cursor'] ?? '',
        ]);
    }

    /**
     * @return list<OrderResponseDTO> Pedidos PARCIAIS: so os campos pedidos em
     *                                $optionalFields vem preenchidos.
     */
    public function getOrderDetail(array $orderSnList, ?array $optionalFields = null): array
    {
        if (empty($orderSnList)) return [];
        $query = [
            'order_sn_list' => implode(',', $orderSnList),
            'response_optional_fields' => implode(',', $optionalFields ?? self::DETAIL_FIELDS),
        ];
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/order/get_order_detail', $query);

        return array_map(
            static fn (array $o): OrderResponseDTO => OrderResponseDTO::fromArray($o),
            $response['response']['order_list'] ?? [],
        );
    }

    /**
     * Atalho: dados de NFe (invoice_data) de UM pedido. Usado pelo pipeline
     * de resolucao de NFe — retorna null quando o pedido nao tem invoice_data.
     */
    public function getInvoiceInfo(string $orderSn): ?InvoiceData
    {
        $details = $this->getOrderDetail([$orderSn], ['invoice_data']);

        return $details[0]->invoiceData ?? null;
    }

    public function getBookingDetail(array $bookingSnList, ?array $optionalFields = null): array
    {
        if (empty($bookingSnList)) return [];
        $query = [
            'booking_sn_list' => implode(',', $bookingSnList),
            'response_optional_fields' => implode(',', $optionalFields ?? ['booking_sn', 'order_sn', 'invoice_data']),
        ];
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/order/get_booking_detail', $query);
        return $response['response']['booking_list'] ?? [];
    }

    public function downloadInvoiceDoc(string $orderSn, string $documentType = 'INVOICE'): array
    {
        return $this->makeRequest(HttpMethod::GET, '/api/v2/order/download_invoice_doc', [
            'order_sn' => $orderSn,
            'document_type' => $documentType,
        ]);
    }

    /**
     * Dispara a geracao dos ZIPs de NFe FBS (1 request_id por document_type).
     * Assincrono: pergunte o status em getFbsInvoicesResult().
     *
     * ENVELOPE ATIPICO: result_list vem na RAIZ, nao em `response`.
     */
    public function generateFbsInvoices(int $start, int $end, int $documentType = 7, int $fileType = 1, int $documentStatus = 1): FbsRequestListResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/order/generate_fbs_invoices', [], [
            'batch_download' => [
                'start' => $start,
                'end' => $end,
                'document_type' => $documentType,
                'file_type' => $fileType,
                'document_status' => $documentStatus,
            ],
        ]);

        return FbsRequestListResponseDTO::fromArray($response);
    }

    /**
     * Status das tarefas: ->resultList[]->status = AVAILABLE|PROCESSING|ERROR.
     * ENVELOPE ATIPICO: result_list vem na RAIZ, nao em `response`.
     */
    public function getFbsInvoicesResult(array $requestIds): FbsRequestListResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/order/get_fbs_invoices_result', [], [
            'request_id_list' => ['request_id' => array_values($requestIds)],
        ]);

        return FbsRequestListResponseDTO::fromArray($response);
    }

    /**
     * Links dos ZIPs prontos. So' chame com request_id AVAILABLE — id ainda
     * PROCESSING devolve erro ERR_DATA_NOT_FOUND.
     *
     * ENVELOPE ATIPICO (terceira variacao!): aqui `response` E' a propria
     * LISTA, nao um objeto que a contem.
     *
     * @return list<FbsDownloadItem>
     */
    public function downloadFbsInvoices(array $requestIds): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/order/download_fbs_invoices', [], [
            'request_id_list' => ['request_id' => array_values($requestIds)],
        ]);

        return array_map(
            static fn (array $r): FbsDownloadItem => FbsDownloadItem::fromArray($r),
            $response['response'] ?? [],
        );
    }

    // -----------------------------------------------------------------------
    // Extras (2026-09): listagem de pacotes/bookings, cancelamento, notas,
    // NF do comprador, split. Nada aqui toca DETAIL_FIELDS.
    // -----------------------------------------------------------------------

    /** Nota interna do vendedor no pedido. */
    public function setNote(string $orderSn, string $note): array
    {
        return $this->makeRequest(HttpMethod::POST, '/api/v2/order/set_note', [], ['order_sn' => $orderSn, 'note' => $note]);
    }

    /**
     * Cancela pedido (total ou parcial). cancel_reason OUT_OF_STOCK exige
     * item_list (item_id + model_id); cancelamento parcial usa
     * partial_cancel_item_list (item_id, model_id, order_item_id,
     * promotion_group_id, model_quantity). Estime antes em getEstimateCancelValue().
     *
     * @param list<array{item_id:int,model_id:int}>|null $itemList
     * @param list<array<string,int>>|null $partialCancelItemList
     */
    public function cancelOrder(string $orderSn, string $cancelReason, ?array $itemList = null, ?array $partialCancelItemList = null): array
    {
        $body = ['order_sn' => $orderSn, 'cancel_reason' => $cancelReason];
        if ($itemList !== null) $body['item_list'] = array_values($itemList);
        if ($partialCancelItemList !== null) $body['partial_cancel_item_list'] = array_values($partialCancelItemList);

        return $this->makeRequest(HttpMethod::POST, '/api/v2/order/cancel_order', [], $body);
    }

    /**
     * Simula o valor de estorno de um cancelamento parcial antes de executar.
     *
     * @param list<array<string,int>> $partialCancelItemList
     */
    public function getEstimateCancelValue(string $orderSn, array $partialCancelItemList): array
    {
        return $this->makeRequest(HttpMethod::POST, '/api/v2/order/get_estimate_cancel_value', [], [
            'order_sn' => $orderSn,
            'partial_cancel_item_list' => array_values($partialCancelItemList),
        ]);
    }

    /** Aceita (ACCEPT) ou rejeita (REJECT) pedido de cancelamento do comprador. */
    public function handleBuyerCancellation(string $orderSn, string $operation): array
    {
        return $this->makeRequest(HttpMethod::POST, '/api/v2/order/handle_buyer_cancellation', [], [
            'order_sn' => $orderSn,
            'operation' => strtoupper($operation),
        ]);
    }

    /**
     * Pedidos prontos pra envio que ainda nao tem package_number
     * (response.order_list[] com order_sn + package_number). Cursor-paginado.
     */
    public function getShipmentList(int $pageSize = 100, string $cursor = ''): array
    {
        $query = ['page_size' => min($pageSize, 100)];
        if ($cursor !== '') $query['cursor'] = $cursor;

        return $this->makeRequest(HttpMethod::GET, '/api/v2/order/get_shipment_list', $query);
    }

    /**
     * Divide o pedido em N pacotes. Cada entrada de package_list traz
     * item_list[] (item_id, model_id, order_item_id, promotion_group_id,
     * model_quantity). Pedido com servico de instalacao nao divide por qtd.
     *
     * @param list<array{item_list:list<array<string,int>>}> $packageList
     */
    public function splitOrder(string $orderSn, array $packageList): array
    {
        return $this->makeRequest(HttpMethod::POST, '/api/v2/order/split_order', [], [
            'order_sn' => $orderSn,
            'package_list' => array_values($packageList),
        ]);
    }

    /** Desfaz o split (volta a 1 pacote). */
    public function unsplitOrder(string $orderSn): array
    {
        return $this->makeRequest(HttpMethod::POST, '/api/v2/order/unsplit_order', [], ['order_sn' => $orderSn]);
    }

    /**
     * Sobe a NF do pedido (multipart, ate 1MB). file_type 1=pdf 2=jpeg
     * 3=png 4=xml. Resposta sem `response` (so error/message/request_id).
     */
    public function uploadInvoiceDoc(string $orderSn, int $fileType, string $contents, string $filename): array
    {
        $apiPath = $this->normalizeApiPath('/api/v2/order/upload_invoice_doc');
        $authQuery = $this->buildAuthQuery($apiPath, false);

        $client = $this->multipartClient()->attach('file', $contents, $filename);
        $response = $client->post($apiPath.'?'.http_build_query($authQuery), [
            'order_sn' => $orderSn,
            'file_type' => (string) $fileType,
        ]);

        $data = $response->json() ?? [];
        if ($response->failed() || ! empty($data['error'])) throw new ShopeeRequestException($response);

        return $data;
    }

    /**
     * Pedidos aguardando NF do vendedor (response.order_sn_list[]). Cursor-paginado.
     */
    public function getPendingBuyerInvoiceOrderList(int $pageSize = 100, string $cursor = ''): array
    {
        $query = ['page_size' => min($pageSize, 100)];
        if ($cursor !== '') $query['cursor'] = $cursor;

        return $this->makeRequest(HttpMethod::GET, '/api/v2/order/get_pending_buyer_invoice_order_list', $query);
    }

    /**
     * Dados de NF informados pelo COMPRADOR (VN/TH/PH apenas — BR devolve
     * vazio). ENVELOPE ATIPICO: invoice_info_list vem na RAIZ.
     *
     * @param list<string> $orderSnList
     */
    public function getBuyerInvoiceInfo(array $orderSnList): array
    {
        return $this->makeRequest(HttpMethod::POST, '/api/v2/order/get_buyer_invoice_info', [], [
            'queries' => array_map(static fn (string $sn): array => ['order_sn' => $sn], array_values($orderSnList)),
        ]);
    }

    /**
     * Aprova/rejeita receita medica de pedido farmacia. Ao rejeitar informe
     * reject_reason_code (1-5; 5 = free_text) e items invalidos.
     *
     * @param array<string,mixed> $extra reject_reason_code, items, pharmacist_name, free_text
     */
    public function handlePrescriptionCheck(string $orderSn, bool $isApproved, array $extra = []): array
    {
        return $this->makeRequest(HttpMethod::POST, '/api/v2/order/handle_prescription_check', [], [
            'order_sn' => $orderSn,
            'is_approved' => $isApproved,
        ] + $extra);
    }

    /**
     * Lista bookings por janela (create_time|update_time, max 15 dias) com
     * filtro booking_status READY_TO_SHIP|PROCESSED|SHIPPED|CANCELLED|MATCHED.
     * Cursor-paginado (response.more + next_cursor).
     */
    public function getBookingList(
        int $timeFrom,
        int $timeTo,
        string $timeRangeField = 'create_time',
        ?string $bookingStatus = null,
        int $pageSize = 50,
        string $cursor = '',
    ): array {
        $query = [
            'time_range_field' => $timeRangeField,
            'time_from' => $timeFrom,
            'time_to' => $timeTo,
            'page_size' => min($pageSize, 100),
        ];
        if ($cursor !== '') $query['cursor'] = $cursor;
        if ($bookingStatus !== null) $query['booking_status'] = $bookingStatus;

        return $this->makeRequest(HttpMethod::GET, '/api/v2/order/get_booking_list', $query);
    }

    /**
     * Busca de pacotes com filtros (package_status, product_location_ids,
     * logistics_channel_ids, fulfillment_type, invoice_pending...), pagination
     * {page_size, cursor} e sort {sort_type, ascending}. Opcoes validas dos
     * filtros vem de getWarehouseFilterConfig().
     *
     * @param array<string,mixed>|null $filter
     * @param array<string,mixed>|null $sort
     */
    public function searchPackageList(?array $filter = null, int $pageSize = 50, string $cursor = '', ?array $sort = null): array
    {
        $body = ['pagination' => ['page_size' => $pageSize, 'cursor' => $cursor]];
        if ($filter !== null) $body['filter'] = $filter;
        if ($sort !== null) $body['sort'] = $sort;

        return $this->makeRequest(HttpMethod::POST, '/api/v2/order/search_package_list', [], $body);
    }

    /** Opcoes de filtro (armazens/product_location e canais) pro searchPackageList(). */
    public function getWarehouseFilterConfig(): array
    {
        return $this->makeRequest(HttpMethod::GET, '/api/v2/order/get_warehouse_filter_config');
    }

    /**
     * Detalhe de ate 50 pacotes (response.package_list[]). Lista vai como
     * string separada por virgula.
     *
     * @param list<string> $packageNumberList
     */
    public function getPackageDetail(array $packageNumberList): array
    {
        if (empty($packageNumberList)) return [];

        return $this->makeRequest(HttpMethod::GET, '/api/v2/order/get_package_detail', [
            'package_number_list' => implode(',', $packageNumberList),
        ]);
    }

}
