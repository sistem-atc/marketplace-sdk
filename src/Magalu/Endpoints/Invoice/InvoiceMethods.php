<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\Endpoints\Invoice;

use SistemAtc\Marketplaces\Magalu\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Magalu\Exceptions\MagaluRequestException;
use Illuminate\Support\Facades\Http;

class InvoiceMethods extends BaseMethods
{
    public function getFulfillmentSignedUrl(string $startDate, string $endDate): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            '/seller/v1/invoices/fulfillment',
            ['start_date' => $startDate, 'end_date' => $endDate]
        );
    }

    public function downloadFulfillmentZip(string $signedUrl): string
    {
        $response = Http::timeout(180)->get($signedUrl);
        if ($response->failed()) throw new MagaluRequestException($response);
        return $response->body();
    }

    public function listForDelivery(string $deliveryId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/seller/v1/deliveries/{$deliveryId}/invoices");
    }
}
