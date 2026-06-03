<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\Endpoints\Invoice;

use SistemAtc\Marketplaces\Tiktok\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use Illuminate\Support\Facades\Http;

class InvoiceMethods extends BaseMethods
{
    public function getByOrderId(string $orderId, string $version = '202407'): array
    {
        $response = $this->makeRequest(
            HttpMethod::GET,
            "/finance/{$version}/orders/{$orderId}/invoices"
        );
        return $response['data']['invoices'] ?? [];
    }

    public function getDocumentUrl(string $orderId, string $invoiceId, string $version = '202407'): array
    {
        $response = $this->makeRequest(
            HttpMethod::GET,
            "/finance/{$version}/orders/{$orderId}/invoices/{$invoiceId}/download_url"
        );
        return $response['data'] ?? [];
    }

    public function downloadFromUrl(string $documentUrl): string
    {
        $response = Http::timeout(60)->get($documentUrl);
        if ($response->failed()) throw new \RuntimeException('Falha ao baixar documento NFe TikTok.');
        return $response->body();
    }
}
