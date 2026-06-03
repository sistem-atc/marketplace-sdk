<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\Endpoints\Billing;

use SistemAtc\Marketplaces\MercadoLivre\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\MercadoLivre\Exceptions\MercadoLivreRequestException;

class BillingMethods extends BaseMethods
{
    public function listPeriods(array $params = []): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            '/billing/integration/monthly/periods',
            $params
        );
    }

    public function summary(string $periodKey, array $params = []): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            "/billing/integration/periods/key/{$periodKey}/summary/details",
            $params
        );
    }

    public function details(string $periodKey, string $group = 'ML', array $params = []): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            "/billing/integration/periods/key/{$periodKey}/group/{$group}/details",
            $params
        );
    }

    public function legalDocument(string $fileId): string
    {
        $response = $this->httpClient->timeout(120)->get("/billing/integration/legal_document/{$fileId}");
        if ($response->failed()) throw new MercadoLivreRequestException($response);
        return (string) $response->body();
    }
}
