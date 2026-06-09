<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\Endpoints\Invoice;

use SistemAtc\Marketplaces\Shopee\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;

class InvoiceMethods extends BaseMethods
{
    /**
     * Faz o upload do XML da nota fiscal (especifico para Brasil e outras regioes).
     */
    public function uploadInvoiceInfo(string $orderSn, string $invoiceNumber, ?string $xmlContent = null): array
    {
        $body = [
            'order_sn' => $orderSn,
            'invoice_number' => $invoiceNumber,
        ];
        if ($xmlContent) {
            $body['file_content'] = base64_encode($xmlContent);
        }

        return $this->makeRequest(HttpMethod::POST, '/api/v2/order/upload_invoice_info', [], $body);
    }
}
