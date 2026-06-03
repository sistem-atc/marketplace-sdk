<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\Endpoints\Order;

use SistemAtc\Marketplaces\Shopee\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;

class OrderMethods extends BaseMethods
{
    public function getOrderList(
        int $timeFrom,
        int $timeTo,
        string $timeRangeField = 'create_time',
        ?string $orderStatus = null,
        int $pageSize = 50,
        string $cursor = '',
    ): array {
        $query = [
            'time_range_field' => $timeRangeField,
            'time_from' => $timeFrom,
            'time_to' => $timeTo,
            'page_size' => min($pageSize, 100),
            'cursor' => $cursor,
        ];

        if ($orderStatus !== null) $query['order_status'] = $orderStatus;

        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/order/get_order_list', $query);
        return [
            'order_list' => $response['response']['order_list'] ?? [],
            'more' => $response['response']['more'] ?? false,
            'next_cursor' => $response['response']['next_cursor'] ?? '',
        ];
    }

    public function getOrderDetail(array $orderSnList, ?array $optionalFields = null): array
    {
        if (empty($orderSnList)) return [];
        $query = [
            'order_sn_list' => implode(',', $orderSnList),
            'response_optional_fields' => implode(',', $optionalFields ?? [
                'buyer_user_id', 'buyer_username', 'recipient_address', 'item_list', 
                'pay_time', 'actual_shipping_fee', 'invoice_data', 'order_status'
            ]),
        ];
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/order/get_order_detail', $query);
        return $response['response']['order_list'] ?? [];
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

    public function generateFbsInvoices(int $start, int $end, int $documentType = 7, int $fileType = 1, int $documentStatus = 1): array
    {
        return $this->makeRequest(HttpMethod::POST, '/api/v2/order/generate_fbs_invoices', [], [
            'batch_download' => [
                'start' => $start,
                'end' => $end,
                'document_type' => $documentType,
                'file_type' => $fileType,
                'document_status' => $documentStatus,
            ],
        ]);
    }

    public function getFbsInvoicesResult(array $requestIds): array
    {
        return $this->makeRequest(HttpMethod::POST, '/api/v2/order/get_fbs_invoices_result', [], [
            'request_id_list' => ['request_id' => array_values($requestIds)],
        ]);
    }

    public function downloadFbsInvoices(array $requestIds): array
    {
        return $this->makeRequest(HttpMethod::POST, '/api/v2/order/download_fbs_invoices', [], [
            'request_id_list' => ['request_id' => array_values($requestIds)],
        ]);
    }
}
