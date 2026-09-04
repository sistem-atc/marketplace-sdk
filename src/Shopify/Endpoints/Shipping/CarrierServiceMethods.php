<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\Endpoints\Shipping;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopify\Bases\BaseMethods;

/**
 * Carrier services (calculo de frete externo via callback_url).
 *
 * Recurso REST: `carrier_service`.
 */
class CarrierServiceMethods extends BaseMethods
{
    /**
     * Lista os carrier services.
     */
    public function list(): array
    {
        return $this->makeRequest(HttpMethod::GET, '/carrier_services');
    }

    /**
     * Recupera um carrier service.
     */
    public function get(int|string $carrierServiceId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/carrier_services/{$carrierServiceId}");
    }

    /**
     * Cria um carrier service (name, callback_url, service_discovery). Embrulha em `carrier_service`.
     *
     * @param  array<string, mixed>  $carrierService
     */
    public function create(array $carrierService): array
    {
        return $this->makeRequest(HttpMethod::POST, '/carrier_services', [], ['carrier_service' => $carrierService]);
    }

    /**
     * Atualiza um carrier service. Embrulha em `carrier_service`.
     *
     * @param  array<string, mixed>  $carrierService
     */
    public function update(int|string $carrierServiceId, array $carrierService): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/carrier_services/{$carrierServiceId}", [], ['carrier_service' => $carrierService]);
    }

    /**
     * Remove um carrier service.
     */
    public function delete(int|string $carrierServiceId): array
    {
        return $this->makeRequest(HttpMethod::DELETE, "/carrier_services/{$carrierServiceId}");
    }
}
