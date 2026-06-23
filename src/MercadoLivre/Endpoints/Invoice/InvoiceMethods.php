<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\Endpoints\Invoice;

use SistemAtc\Marketplaces\MercadoLivre\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\MercadoLivre\Exceptions\MercadoLivreRequestException;

class InvoiceMethods extends BaseMethods
{
    public function getBillingInfo(int|string $orderId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/orders/{$orderId}/billing_info");
    }

    public function getByOrderId(int|string $sellerId, int|string $orderId): ?array
    {
        $response = $this->httpClient->get("/users/{$sellerId}/invoices/orders/{$orderId}");
        if ($response->status() === 404) return null;
        if ($response->failed()) throw new MercadoLivreRequestException($response);
        return $response->json();
    }

    /**
     * Nota fiscal individual por ID (resource do webhook `invoices`):
     * GET /users/{userId}/invoices/{invoiceId}.
     * Retorna null em 404 (NFe inexistente).
     */
    public function getById(int|string $userId, int|string $invoiceId): ?array
    {
        $response = $this->httpClient->get("/users/{$userId}/invoices/{$invoiceId}");
        if ($response->status() === 404) return null;
        if ($response->failed()) throw new MercadoLivreRequestException($response);
        return $response->json();
    }

    public function getByShipment(int|string $sellerId, int|string $shipmentId): ?array
    {
        $response = $this->httpClient->get("/users/{$sellerId}/invoices/shipments/{$shipmentId}");
        if ($response->status() === 404) return null;
        if ($response->failed()) throw new MercadoLivreRequestException($response);
        return $response->json();
    }

    public function downloadXmlByLocation(string $xmlLocation): string
    {
        $response = $this->httpClient->timeout(60)->get($xmlLocation);
        if ($response->failed()) throw new MercadoLivreRequestException($response);
        return (string) $response->body();
    }

    public function batchDownloadInvoicesZip(int|string $sellerId, string $startDate, string $endDate, array $options = []): string
    {
        $url = sprintf('/users/%s/invoices/sites/%s/batch_request/period/stream', $sellerId, $options['site_id'] ?? 'MLB');
        $params = array_merge([
            'start' => str_replace('-', '', $startDate),
            'end' => str_replace('-', '', $endDate),
            'sale' => 'all',
            'return' => 'all',
            'full' => 'all',
            'file_types' => 'xml',
        ], $options);

        $response = $this->httpClient->timeout(600)->get($url, $params);
        if ($response->failed()) throw new MercadoLivreRequestException($response);
        return (string) $response->body();
    }
}
