<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\Endpoints\Inventory;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopify\Bases\BaseMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\Concerns\PaginatesRestByCursor;

/**
 * Estoque: Inventory Items (`inventory_items`), Inventory Levels
 * (`inventory_levels`), Locations (`locations`) e locations_for_move de uma
 * fulfillment order.
 */
class InventoryMethods extends BaseMethods
{
    use PaginatesRestByCursor;

    // ----- inventory_items -----

    /**
     * Lista inventory items por IDs (max 100 por chamada).
     *
     * @param  array<int, int|string>  $ids
     * @param  array<string, mixed>  $params
     */
    public function listItems(array $ids, array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/inventory_items', array_merge(['ids' => implode(',', $ids)], $params));
    }

    /**
     * Itera inventory items pelos IDs seguindo o cursor (page_info).
     *
     * @param  array<int, int|string>  $ids
     * @return \Generator<int, array<string, mixed>>
     */
    public function eachItems(array $ids, int $limit = 100): \Generator
    {
        yield from $this->eachPage('/inventory_items', 'inventory_items', ['ids' => implode(',', $ids)], $limit);
    }

    /**
     * Recupera um inventory item.
     */
    public function getItem(int|string $inventoryItemId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/inventory_items/{$inventoryItemId}");
    }

    /**
     * Atualiza um inventory item (sku, cost, tracked, country_code_of_origin...).
     * Embrulha em `inventory_item`.
     *
     * @param  array<string, mixed>  $inventoryItem
     */
    public function updateItem(int|string $inventoryItemId, array $inventoryItem): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/inventory_items/{$inventoryItemId}", [], ['inventory_item' => $inventoryItem]);
    }

    // ----- inventory_levels -----

    /**
     * Lista inventory levels. Obrigatorio `inventory_item_ids` e/ou
     * `location_ids` (CSV); extras: limit, updated_at_min.
     *
     * @param  array<string, mixed>  $params
     */
    public function listLevels(array $params): array
    {
        return $this->makeRequest(HttpMethod::GET, '/inventory_levels', $params);
    }

    /**
     * Itera inventory levels seguindo o cursor (page_info).
     *
     * @param  array<string, mixed>  $params  inventory_item_ids e/ou location_ids
     * @return \Generator<int, array<string, mixed>>
     */
    public function eachLevels(array $params, int $limit = 250): \Generator
    {
        yield from $this->eachPage('/inventory_levels', 'inventory_levels', $params, $limit);
    }

    /**
     * Ajusta o disponivel de um item numa location (delta). Sem embrulho.
     */
    public function adjustLevel(int|string $inventoryItemId, int|string $locationId, int $availableAdjustment): array
    {
        return $this->makeRequest(HttpMethod::POST, '/inventory_levels/adjust', [], [
            'inventory_item_id' => $inventoryItemId,
            'location_id' => $locationId,
            'available_adjustment' => $availableAdjustment,
        ]);
    }

    /**
     * Conecta um inventory item a uma location. Sem embrulho.
     */
    public function connectLevel(int|string $inventoryItemId, int|string $locationId, bool $relocateIfNecessary = false): array
    {
        return $this->makeRequest(HttpMethod::POST, '/inventory_levels/connect', [], [
            'inventory_item_id' => $inventoryItemId,
            'location_id' => $locationId,
            'relocate_if_necessary' => $relocateIfNecessary,
        ]);
    }

    /**
     * Define o disponivel absoluto de um item numa location. Sem embrulho.
     */
    public function setLevel(int|string $inventoryItemId, int|string $locationId, int $available, bool $disconnectIfNecessary = false): array
    {
        return $this->makeRequest(HttpMethod::POST, '/inventory_levels/set', [], [
            'inventory_item_id' => $inventoryItemId,
            'location_id' => $locationId,
            'available' => $available,
            'disconnect_if_necessary' => $disconnectIfNecessary,
        ]);
    }

    /**
     * Desconecta um inventory item de uma location
     * (DELETE /inventory_levels?inventory_item_id=&location_id=).
     */
    public function deleteLevel(int|string $inventoryItemId, int|string $locationId): array
    {
        return $this->makeRequest(HttpMethod::DELETE, '/inventory_levels', [
            'inventory_item_id' => $inventoryItemId,
            'location_id' => $locationId,
        ]);
    }

    // ----- locations -----

    /**
     * Lista as locations da loja.
     */
    public function listLocations(): array
    {
        return $this->makeRequest(HttpMethod::GET, '/locations');
    }

    /**
     * Conta as locations.
     */
    public function countLocations(): array
    {
        return $this->makeRequest(HttpMethod::GET, '/locations/count');
    }

    /**
     * Recupera uma location.
     */
    public function getLocation(int|string $locationId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/locations/{$locationId}");
    }

    /**
     * Lista os inventory levels de uma location.
     *
     * @param  array<string, mixed>  $params
     */
    public function locationInventoryLevels(int|string $locationId, array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, "/locations/{$locationId}/inventory_levels", $params);
    }

    /**
     * Itera os inventory levels de uma location seguindo o cursor (page_info).
     *
     * @return \Generator<int, array<string, mixed>>
     */
    public function eachLocationInventoryLevels(int|string $locationId, int $limit = 250): \Generator
    {
        yield from $this->eachPage("/locations/{$locationId}/inventory_levels", 'inventory_levels', [], $limit);
    }

    // ----- locations_for_move -----

    /**
     * Locations pra onde uma fulfillment order pode ser movida
     * (GET /fulfillment_orders/{id}/locations_for_move).
     */
    public function locationsForMove(int|string $fulfillmentOrderId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/fulfillment_orders/{$fulfillmentOrderId}/locations_for_move");
    }
}
