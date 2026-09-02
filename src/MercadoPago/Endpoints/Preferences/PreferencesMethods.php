<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\Endpoints\Preferences;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\MercadoPago\Bases\BaseMethods;

/**
 * Preferences — Checkout Pro. A preferencia descreve a compra (items,
 * payer, back_urls, notification_url, external_reference) e devolve
 * `init_point`/`sandbox_init_point` pro comprador pagar.
 *
 * Doc: https://www.mercadopago.com.br/developers/pt/reference/preferences/_checkout_preferences/post
 */
class PreferencesMethods extends BaseMethods
{
    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function create(array $payload): array
    {
        return $this->makeRequest(HttpMethod::POST, '/checkout/preferences', body: $payload);
    }

    /**
     * @return array<string, mixed>
     */
    public function get(string $preferenceId): array
    {
        return $this->makeRequest(HttpMethod::GET, '/checkout/preferences/'.rawurlencode($preferenceId));
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function update(string $preferenceId, array $payload): array
    {
        return $this->makeRequest(HttpMethod::PUT, '/checkout/preferences/'.rawurlencode($preferenceId), body: $payload);
    }

    /**
     * Filtros: external_reference, sponsor_id, marketplace, date_created...
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function search(array $filters = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/checkout/preferences/search', $filters);
    }
}
