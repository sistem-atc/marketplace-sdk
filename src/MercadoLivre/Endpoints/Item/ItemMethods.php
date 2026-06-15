<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\Endpoints\Item;

use SistemAtc\Marketplaces\MercadoLivre\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;

class ItemMethods extends BaseMethods
{
    /**
     * Busca detalhes de um anuncio.
     */
    public function get(string $itemId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/items/{$itemId}");
    }

    /**
     * Cria um novo anuncio.
     */
    public function create(array $data): array
    {
        return $this->makeRequest(HttpMethod::POST, '/items', [], $data);
    }

    /**
     * Atualiza um anuncio existente.
     */
    public function update(string $itemId, array $data): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/items/{$itemId}", [], $data);
    }

    /**
     * Lista IDs de anuncios do seller (paginado).
     */
    public function search(int|string $sellerId, array $filters = []): array
    {
        $query = array_merge(['seller_id' => $sellerId], $filters);
        return $this->makeRequest(HttpMethod::GET, "/users/{$sellerId}/items/search", $query);
    }

    /**
     * Multiget de anuncios: GET /items?ids=MLB1,MLB2&attributes=...
     * Max 20 ids por chamada (limite do ML). Retorna lista de
     * {code, body} — body com os atributos pedidos (ex.: seller_custom_field).
     *
     * @param  array<int, string>  $itemIds
     * @param  array<int, string>  $attributes  ex.: ['id','seller_custom_field','status']
     * @return array<int, array<string, mixed>>
     */
    public function multiGet(array $itemIds, array $attributes = []): array
    {
        if ($itemIds === []) {
            return [];
        }

        $query = ['ids' => implode(',', array_slice($itemIds, 0, 20))];
        if ($attributes !== []) {
            $query['attributes'] = implode(',', $attributes);
        }

        return $this->makeRequest(HttpMethod::GET, '/items', $query);
    }

    /**
     * Altera o status de um anuncio (active, paused, closed).
     */
    public function updateStatus(string $itemId, string $status): array
    {
        return $this->update($itemId, ['status' => $status]);
    }

    /**
     * Atualiza estoque e preco de uma variacao ou do item principal.
     */
    public function updatePriceAndStock(string $itemId, ?float $price = null, ?int $quantity = null): array
    {
        $data = [];
        if ($price !== null) $data['price'] = $price;
        if ($quantity !== null) $data['available_quantity'] = $quantity;

        return $this->update($itemId, $data);
    }

    /**
     * Consulta limites de listagem do seller.
     */
    public function listingCaps(int|string $sellerId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/users/{$sellerId}/listing_caps");
    }
}
