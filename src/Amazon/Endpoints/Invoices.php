<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\DTO\Response\Invoice\ExportResponse;
use SistemAtc\Marketplaces\Amazon\DTO\Response\Invoice\InvoiceDocument;

/**
 * Invoices API v2024-06-19 (Brazil FBA).
 *
 * Recupera as NF-e que a Amazon emite "em nome" do seller (Faturador /
 * Gerenciador de Documentos Fiscais) no marketplace BR. Fluxo assincrono:
 *   1. createExport(marketplaceId, dateStart, dateEnd) -> exportId
 *   2. getExport(exportId) -> poll ate' status=DONE (FATAL/CANCELLED aborta)
 *   3. getDocument(documentId) -> invoicesDocumentUrl (presigned)
 *   4. downloadDocument(url) -> ZIP de XMLs
 *
 * Requer a role SP-API "Tax Invoicing (Restricted)" (senao HTTP 403).
 * Disponivel apenas pra invoices FBA Brasil.
 */
class Invoices
{
    private const BASE = '/tax/invoices/2024-06-19';

    public function __construct(
        private readonly Client $client,
    ) {}

    /**
     * Cria um export de invoices pra uma janela de data.
     *
     * @param  array<string, mixed>  $options  invoiceType e outros filtros opcionais
     * @return array<string, mixed>
     */
    public function createExport(string $marketplaceId, string $dateStart, string $dateEnd, array $options = []): array
    {
        return $this->client->postRestricted(self::BASE.'/invoices/exports', array_merge([
            'marketplaceId' => $marketplaceId,
            'dateStart' => $dateStart,
            'dateEnd' => $dateEnd,
        ], $options));
    }

    /**
     * Status de um export (processingStatus: PROCESSING | DONE | CANCELLED | FATAL).
     *
     * @return array<string, mixed>
     */
    public function getExport(string $exportId): ExportResponse
    {
        return ExportResponse::fromArray($this->client->getRestricted(self::BASE.'/invoices/exports/'.rawurlencode($exportId)));
    }

    /**
     * Lista exports (com filtros opcionais).
     *
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    public function getExports(array $query = []): array
    {
        return $this->client->get(self::BASE.'/invoices/exports', $query);
    }

    /**
     * Metadata de um documento de export — inclui invoicesDocumentUrl (presigned).
     *
     * @return array<string, mixed>
     */
    public function getDocument(string $documentId): InvoiceDocument
    {
        return InvoiceDocument::fromArray($this->client->getRestricted(self::BASE.'/invoices/documents/'.rawurlencode($documentId)));
    }

    /**
     * Valores validos por marketplace (ex. invoiceType).
     *
     * @return array<string, mixed>
     */
    public function getAttributes(string $marketplaceId): array
    {
        return $this->client->get(self::BASE.'/invoices/attributes', ['marketplaceId' => $marketplaceId]);
    }

    /**
     * Baixa o conteudo do documento (ZIP de XMLs) da URL presigned. Sem auth
     * SP-API — a URL ja' vem assinada.
     */
    public function downloadDocument(string $url): string
    {
        return Http::timeout(120)->get($url)->throw()->body();
    }
}
