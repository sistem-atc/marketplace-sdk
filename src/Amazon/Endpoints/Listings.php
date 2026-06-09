<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;

/**
 * Listings Items API (2021-08-01).
 * Permite criar, atualizar e excluir listagens.
 */
class Listings
{
    public function __construct(
        private readonly Client $client,
    ) {}

    /**
     * Recupera detalhes de uma listagem pelo SKU.
     */
    public function getListingsItem(string $sellerId, string $sku, array $query = []): array
    {
        $path = "/listings/2021-08-01/items/{$sellerId}/" . rawurlencode($sku);
        return $this->client->get($path, $query);
    }

    /**
     * Atualiza ou cria uma listagem (PUT).
     */
    public function putListingsItem(string $sellerId, string $sku, array $body): array
    {
        $path = "/listings/2021-08-01/items/{$sellerId}/" . rawurlencode($sku);
        return $this->client->put($path, $body);
    }

    /**
     * Remove uma listagem (DELETE).
     */
    public function deleteListingsItem(string $sellerId, string $sku): array
    {
        $path = "/listings/2021-08-01/items/{$sellerId}/" . rawurlencode($sku);
        return $this->client->delete($path);
    }
}
