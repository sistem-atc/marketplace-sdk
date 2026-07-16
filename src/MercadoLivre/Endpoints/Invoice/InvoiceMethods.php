<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\Endpoints\Invoice;

use Illuminate\Http\Client\Response;
use SistemAtc\Marketplaces\MercadoLivre\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Invoice\BillingInfoResponseDTO;
use SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Invoice\InvoiceResponseDTO;
use SistemAtc\Marketplaces\MercadoLivre\Exceptions\MercadoLivreRequestException;

class InvoiceMethods extends BaseMethods
{
    /**
     * Dados fiscais do comprador (GET /orders/{id}/billing_info) como DTO tipado.
     * Leia campos com `->billingInfo?->additional('FIRST_NAME')` — o ML manda tudo
     * como lista chave-valor. `toArray()` e' LOSSLESS (validado vs 544 reais).
     *
     * Erros (404 sem billing / 403 sem escopo) seguem estourando — o caller
     * decide o que fazer (ver PullMlBillingInfoJob).
     */
    public function getBillingInfo(int|string $orderId): BillingInfoResponseDTO
    {
        return BillingInfoResponseDTO::fromArray(
            $this->makeRequest(HttpMethod::GET, "/orders/{$orderId}/billing_info")
        );
    }

    public function getByOrderId(int|string $sellerId, int|string $orderId): ?InvoiceResponseDTO
    {
        return $this->toInvoiceDto(
            $this->httpClient->get("/users/{$sellerId}/invoices/orders/{$orderId}")
        );
    }

    /**
     * Nota fiscal individual por ID (resource do webhook `invoices`):
     * GET /users/{userId}/invoices/{invoiceId}.
     * Retorna null em 404 (NFe inexistente).
     */
    public function getById(int|string $userId, int|string $invoiceId): ?InvoiceResponseDTO
    {
        return $this->toInvoiceDto(
            $this->httpClient->get("/users/{$userId}/invoices/{$invoiceId}")
        );
    }

    public function getByShipment(int|string $sellerId, int|string $shipmentId): ?InvoiceResponseDTO
    {
        return $this->toInvoiceDto(
            $this->httpClient->get("/users/{$sellerId}/invoices/shipments/{$shipmentId}")
        );
    }

    /**
     * Parse UNICO das 3 chamadas de invoice-metadata: 404 -> null; erro ->
     * exception; senao hidrata a arvore completa (o DTO e' a RAIZ da resposta,
     * com o bloco de emissao em `->attributes`).
     */
    private function toInvoiceDto(Response $response): ?InvoiceResponseDTO
    {
        if ($response->status() === 404) {
            return null;
        }
        if ($response->failed()) {
            throw new MercadoLivreRequestException($response);
        }

        $json = (array) $response->json();

        return InvoiceResponseDTO::fromArray($json);
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
