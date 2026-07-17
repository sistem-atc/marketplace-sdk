<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\Endpoints\Item;

use SistemAtc\Marketplaces\MercadoLivre\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Item\ItemMultiGetResult;
use SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Item\ItemResponseDTO;
use SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Item\ItemSearchResponseDTO;
use SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Item\PriceSuggestionResponseDTO;
use SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Item\SalePriceResponseDTO;

class ItemMethods extends BaseMethods
{
    /**
     * Anuncio completo (GET /items/{id}) como DTO tipado — ponto UNICO de parse.
     * Arvore tipada (preco/estoque/status/SKU/atributos/variacoes/frete);
     * `toArray()` e' LOSSLESS (validado contra 34 anuncios reais da API).
     */
    public function get(string $itemId): ItemResponseDTO
    {
        return ItemResponseDTO::fromArray(
            $this->makeRequest(HttpMethod::GET, "/items/{$itemId}")
        );
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
     * Lista IDs de anuncios do seller (paginado) — devolve so' os MLBs
     * (`->results`), nao os anuncios; hidrate com multiGet(). Use `->scrollId`
     * pra paginar com search_type=scan.
     */
    public function search(int|string $sellerId, array $filters = []): ItemSearchResponseDTO
    {
        $query = array_merge(['seller_id' => $sellerId], $filters);

        return ItemSearchResponseDTO::fromArray(
            $this->makeRequest(HttpMethod::GET, "/users/{$sellerId}/items/search", $query)
        );
    }

    /**
     * Multiget de anuncios: GET /items?ids=MLB1,MLB2&attributes=...
     * Max 20 ids por chamada (limite do ML). Retorna lista de
     * {code, body} — body com os atributos pedidos (ex.: seller_custom_field).
     *
     * @param  array<int, string>  $itemIds
     * @param  array<int, string>  $attributes  ex.: ['id','seller_custom_field','status']
     * @return list<ItemMultiGetResult>
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

        return array_map(
            fn (array $row) => ItemMultiGetResult::fromArray($row),
            (array) $this->makeRequest(HttpMethod::GET, '/items', $query),
        );
    }

    /**
     * Preco de venda EFETIVO do anuncio — inclui campanhas/ofertas do ML
     * ("Oferta imperdivel" etc.), que o campo `price` do item NAO reflete.
     *
     * GET /items/{id}/sale_price?context=channel_marketplace
     *
     * Retorna {amount (PARA, o que o cliente paga), regular_amount (DE, riscado),
     * currency_id, metadata{campaign_id, promotion_type,...}}. Pode dar 404
     * quando nao ha price rule ativa — o chamador deve tratar (fallback no
     * `price` do item).
     *
     */
    public function salePrice(string $itemId, string $context = 'channel_marketplace'): SalePriceResponseDTO
    {
        return SalePriceResponseDTO::fromArray(
            $this->makeRequest(HttpMethod::GET, "/items/{$itemId}/sale_price", ['context' => $context])
        );
    }

    /**
     * Sugestao de preco (Price Suggestion API) de um anuncio:
     * GET /suggestions/items/{itemId}/details — retorna status, ratio,
     * current_price / suggested_price / lowest_price (cada um {amount, ...}).
     *
     * GOTCHA: o path do webhook price_suggestion (/marketplace/benchmarks/items/
     * {id}/details) da 403 "Invalid caller.id"; o endpoint que responde 200 e'
     * este (/suggestions/...).
     *
     */
    public function priceSuggestion(string $itemId): PriceSuggestionResponseDTO
    {
        return PriceSuggestionResponseDTO::fromArray(
            $this->makeRequest(HttpMethod::GET, "/suggestions/items/{$itemId}/details")
        );
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
