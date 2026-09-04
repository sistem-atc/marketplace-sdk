<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\Endpoints\Payments;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopify\Bases\BaseMethods;

/**
 * Certificados Apple Pay (Sales Channel SDK).
 *
 * Recurso REST: `apple_pay_certificate`.
 */
class ApplePayCertificateMethods extends BaseMethods
{
    /**
     * Recupera um certificado.
     */
    public function get(int|string $certificateId, array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, "/apple_pay_certificates/{$certificateId}", $params);
    }

    /**
     * Gera o CSR (certificate signing request) do certificado.
     */
    public function csr(int|string $certificateId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/apple_pay_certificates/{$certificateId}/csr");
    }

    /**
     * Cria um certificado. Embrulha em `apple_pay_certificate`.
     *
     * @param  array<string, mixed>  $certificate
     */
    public function create(array $certificate = []): array
    {
        return $this->makeRequest(HttpMethod::POST, '/apple_pay_certificates', [], ['apple_pay_certificate' => $certificate]);
    }

    /**
     * Atualiza um certificado (status/merchant_id/encoded_signed_certificate). Embrulha em `apple_pay_certificate`.
     *
     * @param  array<string, mixed>  $certificate
     */
    public function update(int|string $certificateId, array $certificate): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/apple_pay_certificates/{$certificateId}", [], ['apple_pay_certificate' => $certificate]);
    }

    /**
     * Remove um certificado.
     */
    public function delete(int|string $certificateId): array
    {
        return $this->makeRequest(HttpMethod::DELETE, "/apple_pay_certificates/{$certificateId}");
    }
}
