<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\Endpoints\Order;

use SistemAtc\Marketplaces\Shopee\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
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
}
