<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\Endpoints\Fulfillment;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopify\Bases\BaseMethods;

/**
 * Fulfillment Services (`fulfillment_services`) — servicos de fulfillment
 * registrados pelo app (3PL, etc).
 */
class FulfillmentServiceMethods extends BaseMethods
{
    /**
     * Lista os fulfillment services. `scope`: current_client (default) | all.
     *
     * @param  array<string, mixed>  $params
     */
    public function list(array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/fulfillment_services', $params);
    }

    /**
     * Recupera um fulfillment service.
     */
    public function get(int|string $fulfillmentServiceId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/fulfillment_services/{$fulfillmentServiceId}");
    }

    /**
     * Cria um fulfillment service. Embrulha em `fulfillment_service`.
     *
     * @param  array<string, mixed>  $fulfillmentService
     */
    public function create(array $fulfillmentService): array
    {
        return $this->makeRequest(HttpMethod::POST, '/fulfillment_services', [], ['fulfillment_service' => $fulfillmentService]);
    }

    /**
     * Atualiza um fulfillment service. Embrulha em `fulfillment_service`.
     *
     * @param  array<string, mixed>  $fulfillmentService
     */
    public function update(int|string $fulfillmentServiceId, array $fulfillmentService): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/fulfillment_services/{$fulfillmentServiceId}", [], ['fulfillment_service' => $fulfillmentService]);
    }

    /**
     * Exclui um fulfillment service.
     */
    public function delete(int|string $fulfillmentServiceId): array
    {
        return $this->makeRequest(HttpMethod::DELETE, "/fulfillment_services/{$fulfillmentServiceId}");
    }
}
