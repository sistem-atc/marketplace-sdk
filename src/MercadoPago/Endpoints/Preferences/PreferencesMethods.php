<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\Endpoints\Preferences;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\MercadoPago\Bases\BaseMethods;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\Preferences\PreferenceResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\Preferences\PreferenceSearchResponseDTO;

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
     */
    public function create(array $payload): PreferenceResponseDTO
    {
        return PreferenceResponseDTO::fromArray($this->makeRequest(HttpMethod::POST, '/checkout/preferences', body: $payload));
    }

    public function get(string $preferenceId): PreferenceResponseDTO
    {
        return PreferenceResponseDTO::fromArray($this->makeRequest(HttpMethod::GET, '/checkout/preferences/'.rawurlencode($preferenceId)));
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function update(string $preferenceId, array $payload): PreferenceResponseDTO
    {
        return PreferenceResponseDTO::fromArray($this->makeRequest(HttpMethod::PUT, '/checkout/preferences/'.rawurlencode($preferenceId), body: $payload));
    }

    /**
     * Filtros: external_reference, sponsor_id, marketplace, date_created...
     *
     * @param  array<string, mixed>  $filters
     */
    public function search(array $filters = []): PreferenceSearchResponseDTO
    {
        return PreferenceSearchResponseDTO::fromArray($this->makeRequest(HttpMethod::GET, '/checkout/preferences/search', $filters));
    }
}
