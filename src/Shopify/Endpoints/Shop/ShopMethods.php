<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\Endpoints\Shop;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopify\Bases\BaseMethods;

/**
 * Dados da loja e cadastros globais (Admin API REST — `shop`, `policy`,
 * `province`, `shipping_zone`, `user`, `storefront_access_token`).
 */
class ShopMethods extends BaseMethods
{
    /**
     * Configuracao da loja (nome, moeda, timezone, plano, dominio...).
     *
     * @param  array<string, mixed>  $params  ex.: fields
     */
    public function get(array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/shop', $params);
    }

    /**
     * Politicas da loja (reembolso, privacidade, termos...).
     */
    public function policies(): array
    {
        return $this->makeRequest(HttpMethod::GET, '/policies');
    }

    /**
     * Zonas de frete configuradas na loja.
     *
     * @param  array<string, mixed>  $params  ex.: fields
     */
    public function shippingZones(array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/shipping_zones', $params);
    }

    // ---------------------------------------------------------------
    // Provinces (estados de um pais cadastrado em countries/<id>)
    // ---------------------------------------------------------------

    /**
     * Lista estados/provincias de um pais.
     *
     * @param  array<string, mixed>  $params  ex.: since_id, fields
     */
    public function listProvinces(int|string $countryId, array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, "/countries/{$countryId}/provinces", $params);
    }

    /**
     * Conta estados/provincias de um pais.
     */
    public function countProvinces(int|string $countryId): int
    {
        $response = $this->makeRequest(HttpMethod::GET, "/countries/{$countryId}/provinces/count");

        return $response['count'] ?? 0;
    }

    /**
     * Recupera um estado/provincia.
     *
     * @param  array<string, mixed>  $params  ex.: fields
     */
    public function getProvince(int|string $countryId, int|string $provinceId, array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, "/countries/{$countryId}/provinces/{$provinceId}", $params);
    }

    /**
     * Atualiza um estado/provincia (ex.: tax).
     *
     * @param  array<string, mixed>  $province
     */
    public function updateProvince(int|string $countryId, int|string $provinceId, array $province): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/countries/{$countryId}/provinces/{$provinceId}", [], ['province' => $province]);
    }

    // ---------------------------------------------------------------
    // Users (staff da loja — exige Shopify Plus)
    // ---------------------------------------------------------------

    /**
     * Lista usuarios (staff) da loja. Exige Shopify Plus.
     *
     * @param  array<string, mixed>  $params  ex.: limit, page_info
     */
    public function listUsers(array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/users', $params);
    }

    /**
     * Recupera um usuario (staff). Exige Shopify Plus.
     */
    public function getUser(int|string $userId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/users/{$userId}");
    }

    /**
     * Usuario dono do token atual (users/current). Exige Shopify Plus.
     */
    public function currentUser(): array
    {
        return $this->makeRequest(HttpMethod::GET, '/users/current');
    }

    // ---------------------------------------------------------------
    // Storefront access tokens (tokens da Storefront API)
    // ---------------------------------------------------------------

    /**
     * Lista tokens da Storefront API.
     */
    public function listStorefrontAccessTokens(): array
    {
        return $this->makeRequest(HttpMethod::GET, '/storefront_access_tokens');
    }

    /**
     * Cria um token da Storefront API. Ex.: ['title' => 'Bunker'].
     *
     * @param  array<string, mixed>  $token
     */
    public function createStorefrontAccessToken(array $token): array
    {
        return $this->makeRequest(HttpMethod::POST, '/storefront_access_tokens', [], ['storefront_access_token' => $token]);
    }

    /**
     * Revoga um token da Storefront API.
     */
    public function deleteStorefrontAccessToken(int|string $tokenId): array
    {
        return $this->makeRequest(HttpMethod::DELETE, "/storefront_access_tokens/{$tokenId}");
    }
}
