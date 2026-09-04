<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\Endpoints\Fulfillment;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Magalu\Bases\BaseMethods;

/**
 * Documentos fiscais emitidos pelo Magalu Entregas (NF-e/CT-e do fulfillment)
 * — /logistics/v1/fiscal-documents, base `services.magalu.com`.
 *
 * Consulta por chave de acesso ou exportacao em lote (zip de XML ou PDF):
 * requestBatchDownload() devolve `taskId`; getBatchExport() traz o status e a
 * `url_download` quando pronto. O callback de status
 * (POST .../batch-export/status-callback) e' RECEBIDO pelo seller, nao
 * chamado — por isso nao existe metodo aqui.
 */
class FiscalDocumentMethods extends BaseMethods
{
    /**
     * Documento por chave (GET /logistics/v1/fiscal-documents/{access_key}).
     *
     * `format`: xml|pdf; flags `include_approved` (default true),
     * `include_canceled`, `include_approved_canceled`, `include_reference`,
     * `encoding_base64` (default true). Resposta `{xml: "..."}`.
     *
     * @param array<string, mixed> $options
     * @return array<string, mixed>
     */
    public function getByAccessKey(string $accessKey, string $format = 'xml', array $options = []): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            $this->servicesUrl("/logistics/v1/fiscal-documents/{$accessKey}"),
            array_merge(['format' => $format], $options),
        );
    }

    /**
     * Solicita exportacao em lote (POST /logistics/v1/fiscal-documents/batch-download).
     *
     * `filters.date_range {initial, end}` obrigatorio; `filters.identification`
     * e `filters.attributes` opcionais. `zipContents`: XML|PDF.
     *
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function requestBatchDownload(array $filters, string $zipContents = 'XML'): array
    {
        return $this->makeRequest(HttpMethod::POST, $this->servicesUrl('/logistics/v1/fiscal-documents/batch-download'), [], [
            'zip_contents' => $zipContents,
            'filters' => $filters,
        ]);
    }

    /**
     * Status da exportacao (GET /logistics/v1/fiscal-documents/batch-export/{task_id}).
     *
     * @return array<string, mixed>
     */
    public function getBatchExport(string $taskId): array
    {
        return $this->makeRequest(HttpMethod::GET, $this->servicesUrl("/logistics/v1/fiscal-documents/batch-export/{$taskId}"));
    }
}
