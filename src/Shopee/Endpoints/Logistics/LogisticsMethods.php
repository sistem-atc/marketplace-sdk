<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\Endpoints\Logistics;

use SistemAtc\Marketplaces\Shopee\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
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
}
