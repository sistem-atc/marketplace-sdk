<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\Endpoints\Billing;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopify\Bases\BaseMethods;

/**
 * Cobrancas avulsas (one-time) e creditos do app na Billing API.
 *
 * Recursos REST: `application_charge`, `application_credit`.
 * Obs.: `application_credit` so' aceita leitura via REST (criacao migrou
 * para a mutation GraphQL `appCreditCreate`).
 */
class ApplicationChargeMethods extends BaseMethods
{
    /**
     * Lista as cobrancas avulsas.
     *
     * @param  array<string, mixed>  $params  since_id, fields
     */
    public function list(array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/application_charges', $params);
    }

    /**
     * Recupera uma cobranca avulsa.
     */
    public function get(int|string $chargeId, array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, "/application_charges/{$chargeId}", $params);
    }

    /**
     * Cria uma cobranca avulsa (name, price, return_url, test). Embrulha em `application_charge`.
     *
     * @param  array<string, mixed>  $charge
     */
    public function create(array $charge): array
    {
        return $this->makeRequest(HttpMethod::POST, '/application_charges', [], ['application_charge' => $charge]);
    }

    /**
     * Lista os creditos concedidos pelo app.
     *
     * @param  array<string, mixed>  $params  fields
     */
    public function listCredits(array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/application_credits', $params);
    }

    /**
     * Recupera um credito.
     */
    public function getCredit(int|string $creditId, array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, "/application_credits/{$creditId}", $params);
    }
}
