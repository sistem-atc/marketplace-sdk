<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Support\FlattensCsvQuery;

/**
 * Data Kiosk API 2023-11-15 — consultas GraphQL assíncronas sobre os
 * datasets analíticos da Amazon (sales & traffic, economics…). Fluxo:
 *   createQuery(graphql) → getQuery(id) até DONE → getDocument(dataDocumentId)
 *   → downloadDocument(url) (JSONL).
 */
class DataKiosk
{
    use FlattensCsvQuery;

    private const BASE = '/dataKiosk/2023-11-15';

    public function __construct(
        private readonly Client $client,
    ) {}

    /**
     * Lista consultas (GET /dataKiosk/2023-11-15/queries). Query:
     * processingStatuses[] (CANCELLED, DONE, FATAL, IN_PROGRESS, IN_QUEUE),
     * pageSize (1–100), createdSince, createdUntil, paginationToken (com
     * token os demais filtros são ignorados). Retorna `queries[]` +
     * `pagination.nextToken`. Rate limit: 0.0222 req/s + burst 10.
     *
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    public function getQueries(array $query = []): array
    {
        return $this->client->get(self::BASE.'/queries', $this->csv($query));
    }

    /**
     * Cria uma consulta (POST /dataKiosk/2023-11-15/queries). `query` é o
     * GraphQL (≤ 8000 chars); `paginationToken` continua uma consulta anterior
     * que devolveu `pagination.nextToken`. Retorna `queryId`.
     * Rate limit: 0.0167 req/s + burst 15.
     *
     * @return array<string, mixed>
     */
    public function createQuery(string $query, ?string $paginationToken = null): array
    {
        $body = ['query' => $query];
        if ($paginationToken !== null) {
            $body['paginationToken'] = $paginationToken;
        }

        return $this->client->post(self::BASE.'/queries', $body);
    }

    /**
     * Status de uma consulta (GET /dataKiosk/2023-11-15/queries/{queryId}):
     * processingStatus, dataDocumentId (resultado), errorDocumentId,
     * pagination.nextToken. Rate limit: 2 req/s + burst 15.
     *
     * @return array<string, mixed>
     */
    public function getQuery(string $queryId): array
    {
        return $this->client->get(self::BASE.'/queries/'.rawurlencode($queryId));
    }

    /**
     * Cancela uma consulta IN_QUEUE/IN_PROGRESS
     * (DELETE /dataKiosk/2023-11-15/queries/{queryId}). Resposta 204 vazia.
     * Rate limit: 0.0222 req/s + burst 10.
     *
     * @return array<string, mixed>
     */
    public function cancelQuery(string $queryId): array
    {
        return $this->client->delete(self::BASE.'/queries/'.rawurlencode($queryId));
    }

    /**
     * URL pré-assinada do documento de resultado/erro
     * (GET /dataKiosk/2023-11-15/documents/{documentId}) → {documentId,
     * documentUrl}. Rate limit: 0.0167 req/s + burst 15.
     *
     * @return array<string, mixed>
     */
    public function getDocument(string $documentId): array
    {
        return $this->client->get(self::BASE.'/documents/'.rawurlencode($documentId));
    }

    /**
     * Baixa o documento (JSONL) da URL pré-assinada. Sem auth SP-API.
     */
    public function downloadDocument(string $url): string
    {
        return Http::timeout(120)->get($url)->throw()->body();
    }
}
