<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\Endpoints\Payments;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopify\Bases\BaseMethods;

/**
 * Shopify Payments — evidencias de chargeback
 * (`shopify_payments/disputes/{dispute_id}/dispute_evidences`) e upload de
 * arquivos (`.../dispute_file_uploads`). Exige Shopify Payments ativo.
 */
class DisputeEvidenceMethods extends BaseMethods
{
    /**
     * Recupera a evidencia de uma disputa.
     */
    public function get(int|string $disputeId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/shopify_payments/disputes/{$disputeId}/dispute_evidences");
    }

    /**
     * Atualiza a evidencia (PUT). Embrulha em `dispute_evidence` — ex.:
     * ['customer_email_address' => ..., 'uncategorized_text' => ..., 'submit_evidence' => true].
     *
     * @param  array<string, mixed>  $disputeEvidence
     */
    public function update(int|string $disputeId, array $disputeEvidence): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/shopify_payments/disputes/{$disputeId}/dispute_evidences", [], ['dispute_evidence' => $disputeEvidence]);
    }

    /**
     * Envia um arquivo de evidencia (POST). Embrulha em `dispute_file_upload` —
     * ['evidence_type' => 'shipping_documentation', 'file' => base64, 'filename' => ...].
     *
     * @param  array<string, mixed>  $fileUpload
     */
    public function uploadFile(int|string $disputeId, array $fileUpload): array
    {
        return $this->makeRequest(HttpMethod::POST, "/shopify_payments/disputes/{$disputeId}/dispute_file_uploads", [], ['dispute_file_upload' => $fileUpload]);
    }

    /**
     * Remove um arquivo de evidencia.
     */
    public function deleteFile(int|string $disputeId, int|string $fileUploadId): array
    {
        return $this->makeRequest(HttpMethod::DELETE, "/shopify_payments/disputes/{$disputeId}/dispute_file_uploads/{$fileUploadId}");
    }
}
