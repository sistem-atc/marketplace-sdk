<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;

/**
 * A+ Content API v2020-11-01.
 *
 * Path base: /aplus/2020-11-01. `marketplaceId` é query obrigatória em TODAS
 * as operações (inclusive POSTs — anexada na URL). Rate limit do modelo:
 * 10 req/s, burst 10. Respostas sem envelope `payload`; listas paginam por
 * `nextPageToken` → `$query['pageToken']`.
 */
class AplusContent
{
    private const BASE = '/aplus/2020-11-01';

    public function __construct(
        private readonly Client $client,
    ) {}

    /**
     * Lista documentos A+ do seller. GET /contentDocuments — 10 req/s. Dado em `contentMetadataRecords`.
     *
     * @param  array<string, mixed>  $query  pageToken
     */
    public function searchContentDocuments(string $marketplaceId, array $query = []): array
    {
        return $this->client->get(self::BASE.'/contentDocuments', array_merge(['marketplaceId' => $marketplaceId], $query));
    }

    /**
     * Cria documento A+. POST /contentDocuments?marketplaceId= — 10 req/s. Retorna `contentReferenceKey`.
     *
     * @param  array<string, mixed>  $body  PostContentDocumentRequest ({contentDocument: {...}})
     */
    public function createContentDocument(string $marketplaceId, array $body): array
    {
        return $this->client->post(
            self::BASE.'/contentDocuments?'.http_build_query(['marketplaceId' => $marketplaceId]),
            $body,
        );
    }

    /**
     * Detalhe do documento. GET /contentDocuments/{key} — 10 req/s. Dado em `contentRecord`.
     *
     * @param  list<string>  $includedDataSet  CONTENTS e/ou METADATA
     */
    public function getContentDocument(string $contentReferenceKey, string $marketplaceId, array $includedDataSet = ['CONTENTS', 'METADATA']): array
    {
        return $this->client->get(self::BASE.'/contentDocuments/'.rawurlencode($contentReferenceKey), [
            'marketplaceId' => $marketplaceId,
            'includedDataSet' => implode(',', $includedDataSet),
        ]);
    }

    /**
     * Atualiza documento (cria nova versão em rascunho). POST /contentDocuments/{key}?marketplaceId= — 10 req/s.
     *
     * @param  array<string, mixed>  $body  PostContentDocumentRequest
     */
    public function updateContentDocument(string $contentReferenceKey, string $marketplaceId, array $body): array
    {
        return $this->client->post(
            self::BASE.'/contentDocuments/'.rawurlencode($contentReferenceKey).'?'.http_build_query(['marketplaceId' => $marketplaceId]),
            $body,
        );
    }

    /**
     * ASINs vinculados ao documento. GET /contentDocuments/{key}/asins — 10 req/s. Dado em `asinMetadataSet`.
     *
     * @param  array<string, mixed>  $query  includedDataSet (METADATA), asinSet (csv), pageToken
     */
    public function listContentDocumentAsinRelations(string $contentReferenceKey, string $marketplaceId, array $query = []): array
    {
        return $this->client->get(
            self::BASE.'/contentDocuments/'.rawurlencode($contentReferenceKey).'/asins',
            array_merge(['marketplaceId' => $marketplaceId], $query),
        );
    }

    /**
     * Substitui o conjunto de ASINs vinculados. POST /contentDocuments/{key}/asins?marketplaceId= — 10 req/s.
     *
     * @param  array<string, mixed>  $body  PostContentDocumentAsinRelationsRequest ({asinSet: [...]})
     */
    public function postContentDocumentAsinRelations(string $contentReferenceKey, string $marketplaceId, array $body): array
    {
        return $this->client->post(
            self::BASE.'/contentDocuments/'.rawurlencode($contentReferenceKey).'/asins?'.http_build_query(['marketplaceId' => $marketplaceId]),
            $body,
        );
    }

    /**
     * Valida um documento contra um conjunto de ASINs (sem persistir). POST /contentAsinValidations — 10 req/s.
     *
     * @param  array<string, mixed>  $body  PostContentDocumentRequest
     * @param  list<string>  $asinSet
     */
    public function validateContentDocumentAsinRelations(string $marketplaceId, array $body, array $asinSet = []): array
    {
        $q = ['marketplaceId' => $marketplaceId];
        if ($asinSet !== []) {
            $q['asinSet'] = implode(',', $asinSet);
        }

        return $this->client->post(self::BASE.'/contentAsinValidations?'.http_build_query($q), $body);
    }

    /**
     * Histórico de publicação de um ASIN. GET /contentPublishRecords — 10 req/s. Dado em `publishRecordList`.
     *
     * @param  array<string, mixed>  $query  pageToken
     */
    public function searchContentPublishRecords(string $marketplaceId, string $asin, array $query = []): array
    {
        return $this->client->get(self::BASE.'/contentPublishRecords', array_merge([
            'marketplaceId' => $marketplaceId,
            'asin' => $asin,
        ], $query));
    }

    /** Submete o documento pra aprovação. POST /contentDocuments/{key}/approvalSubmissions?marketplaceId= — 10 req/s. */
    public function postContentDocumentApprovalSubmission(string $contentReferenceKey, string $marketplaceId): array
    {
        return $this->client->post(
            self::BASE.'/contentDocuments/'.rawurlencode($contentReferenceKey).'/approvalSubmissions?'.http_build_query(['marketplaceId' => $marketplaceId]),
        );
    }

    /** Suspende (despublica) o documento. POST /contentDocuments/{key}/suspendSubmissions?marketplaceId= — 10 req/s. */
    public function postContentDocumentSuspendSubmission(string $contentReferenceKey, string $marketplaceId): array
    {
        return $this->client->post(
            self::BASE.'/contentDocuments/'.rawurlencode($contentReferenceKey).'/suspendSubmissions?'.http_build_query(['marketplaceId' => $marketplaceId]),
        );
    }
}
