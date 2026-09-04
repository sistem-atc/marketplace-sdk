<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use Illuminate\Support\Facades\Http;
use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\Support\FlattensCsvQuery;

/**
 * Feeds API 2021-06-30 — envio em lote (preço, estoque, listings, confirmação
 * de envio, NF-e BR…). Fluxo:
 *   1. createFeedDocument(contentType) → {feedDocumentId, url}
 *   2. uploadFeedDocument(url, conteúdo, contentType)  (PUT direto, sem auth)
 *   3. createFeed(feedType, marketplaceIds, feedDocumentId) → {feedId}
 *   4. getFeed(feedId) até processingStatus DONE → resultFeedDocumentId
 *   5. getFeedDocument(resultFeedDocumentId) → url → downloadFeedDocument(url)
 */
class Feeds
{
    use FlattensCsvQuery;

    private const BASE = '/feeds/2021-06-30';

    public function __construct(
        private readonly Client $client,
    ) {}

    /**
     * Lista feeds (GET /feeds/2021-06-30/feeds). Query: feedTypes[],
     * marketplaceIds[], pageSize (1–100), processingStatuses[] (CANCELLED,
     * DONE, FATAL, IN_PROGRESS, IN_QUEUE), createdSince, createdUntil,
     * nextToken (com nextToken os demais filtros são ignorados). Arrays viram
     * csv. Retorna `feeds[]` + `nextToken`. Rate limit: 0.0222 req/s + burst 10.
     *
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    public function getFeeds(array $query = []): array
    {
        return $this->client->get(self::BASE.'/feeds', $this->csv($query));
    }

    /**
     * Cria o feed (POST /feeds/2021-06-30/feeds) apontando pro documento já
     * enviado. Extras: feedOptions (map string→string). Retorna `feedId`.
     * Rate limit: 0.0083 req/s + burst 15.
     *
     * @param  list<string>  $marketplaceIds
     * @param  array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function createFeed(string $feedType, array $marketplaceIds, string $inputFeedDocumentId, array $options = []): array
    {
        return $this->client->post(self::BASE.'/feeds', [
            'feedType' => $feedType,
            'marketplaceIds' => $marketplaceIds,
            'inputFeedDocumentId' => $inputFeedDocumentId,
        ] + $options);
    }

    /**
     * Status de um feed (GET /feeds/2021-06-30/feeds/{feedId}): processingStatus,
     * datas, resultFeedDocumentId. Rate limit: 2 req/s + burst 15.
     *
     * @return array<string, mixed>
     */
    public function getFeed(string $feedId): array
    {
        return $this->client->get(self::BASE.'/feeds/'.rawurlencode($feedId));
    }

    /**
     * Cancela um feed ainda IN_QUEUE (DELETE /feeds/2021-06-30/feeds/{feedId}).
     * Resposta 200 vazia. Rate limit: 2 req/s + burst 15.
     *
     * @return array<string, mixed>
     */
    public function cancelFeed(string $feedId): array
    {
        return $this->client->delete(self::BASE.'/feeds/'.rawurlencode($feedId));
    }

    /**
     * Reserva um documento de feed (POST /feeds/2021-06-30/documents) e recebe
     * a URL pré-assinada pro upload. `contentType` tem que ser IDÊNTICO ao
     * usado no upload (ex.: `text/xml; charset=UTF-8`,
     * `application/json; charset=UTF-8`, `text/tab-separated-values; charset=UTF-8`).
     * Retorna {feedDocumentId, url}. Rate limit: 0.5 req/s + burst 15.
     *
     * @return array<string, mixed>
     */
    public function createFeedDocument(string $contentType): array
    {
        return $this->client->post(self::BASE.'/documents', ['contentType' => $contentType]);
    }

    /**
     * Sobe o conteúdo do feed na URL pré-assinada (PUT binário, sem token
     * SP-API — a URL já vem assinada). O `Content-Type` PRECISA ser o mesmo
     * string passado em createFeedDocument, senão a S3 recusa (403).
     * Retorna true em 2xx; lança em erro.
     */
    public function uploadFeedDocument(string $url, string $content, string $contentType): bool
    {
        return Http::timeout(120)
            ->withBody($content, $contentType)
            ->put($url)
            ->throw()
            ->successful();
    }

    /**
     * Metadados do documento de resultado (GET /feeds/2021-06-30/documents/{id}):
     * {feedDocumentId, url, compressionAlgorithm?}. Com
     * `enableContentEncodingUrlHeader=true` a URL devolve Content-Encoding gzip
     * (descompressão automática pelo cliente HTTP). Rate limit: 0.0222 req/s + burst 10.
     *
     * @return array<string, mixed>
     */
    public function getFeedDocument(string $feedDocumentId, ?bool $enableContentEncodingUrlHeader = null): array
    {
        $query = $enableContentEncodingUrlHeader === null
            ? []
            : $this->csv(['enableContentEncodingUrlHeader' => $enableContentEncodingUrlHeader]);

        return $this->client->get(self::BASE.'/documents/'.rawurlencode($feedDocumentId), $query);
    }

    /**
     * Baixa o documento de resultado da URL pré-assinada (sem auth). Se
     * `compressionAlgorithm` for GZIP, descomprime.
     */
    public function downloadFeedDocument(string $url, ?string $compressionAlgorithm = null): string
    {
        $body = Http::timeout(120)->get($url)->throw()->body();

        if (strtoupper((string) $compressionAlgorithm) === 'GZIP') {
            $decoded = @gzdecode($body);
            if ($decoded !== false) {
                return $decoded;
            }
        }

        return $body;
    }
}
