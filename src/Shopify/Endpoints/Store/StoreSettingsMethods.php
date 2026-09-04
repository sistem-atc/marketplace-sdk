<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\Endpoints\Store;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopify\Bases\BaseMethods;

/**
 * Configuracoes da loja: escopos de acesso, moedas, paises (impostos por
 * regiao) e chamadas a API deprecadas.
 *
 * Recursos REST: `access_scope`, `currency`, `country`, `deprecated_api_call`.
 */
class StoreSettingsMethods extends BaseMethods
{
    /**
     * Lista os escopos de acesso concedidos ao app (`/admin/oauth/access_scopes.json`).
     *
     * Este endpoint vive fora de `/admin/api/{version}`; o path e' remontado
     * a partir do dominio da loja.
     */
    public function accessScopes(): array
    {
        $shop = $this->integration->getMarketplaceSettings()['shop_domain'] ?? '';

        return $this->makeRequest(HttpMethod::GET, "https://{$shop}/admin/oauth/access_scopes");
    }

    /**
     * Lista as moedas habilitadas na loja.
     */
    public function currencies(): array
    {
        return $this->makeRequest(HttpMethod::GET, '/currencies');
    }

    /**
     * Lista as chamadas a versoes deprecadas da API feitas pelo app.
     */
    public function deprecatedApiCalls(): array
    {
        return $this->makeRequest(HttpMethod::GET, '/deprecated_api_calls');
    }

    /**
     * Lista os paises configurados para envio/impostos.
     *
     * @param  array<string, mixed>  $params  since_id, fields
     */
    public function listCountries(array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/countries', $params);
    }

    /**
     * Total de paises configurados.
     */
    public function countCountries(): array
    {
        return $this->makeRequest(HttpMethod::GET, '/countries/count');
    }

    /**
     * Recupera um pais.
     */
    public function getCountry(int|string $countryId, array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, "/countries/{$countryId}", $params);
    }

    /**
     * Cria um pais. Embrulha em `country`.
     *
     * @param  array<string, mixed>  $country
     */
    public function createCountry(array $country): array
    {
        return $this->makeRequest(HttpMethod::POST, '/countries', [], ['country' => $country]);
    }

    /**
     * Atualiza um pais. Embrulha em `country`.
     *
     * @param  array<string, mixed>  $country
     */
    public function updateCountry(int|string $countryId, array $country): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/countries/{$countryId}", [], ['country' => $country]);
    }

    /**
     * Remove um pais.
     */
    public function deleteCountry(int|string $countryId): array
    {
        return $this->makeRequest(HttpMethod::DELETE, "/countries/{$countryId}");
    }

    /**
     * URL absoluta (access_scopes fora do prefixo versionado) passa intacta;
     * o resto segue a normalizacao padrao (barra inicial + `.json`).
     */
    protected function normalizePath(string $path): string
    {
        if (str_starts_with($path, 'https://')) {
            return str_ends_with($path, '.json') ? $path : $path.'.json';
        }

        return parent::normalizePath($path);
    }
}
