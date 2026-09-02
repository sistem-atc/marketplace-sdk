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
     * Cria um novo anuncio. Devolve o anuncio CRIADO (mesma arvore do get()).
     */
    public function create(array $data): ItemResponseDTO
    {
        return ItemResponseDTO::fromArray(
            $this->makeRequest(HttpMethod::POST, '/items', [], $data)
        );
    }

    /**
     * Atualiza um anuncio existente. Devolve o anuncio ATUALIZADO.
     */
    public function update(string $itemId, array $data): ItemResponseDTO
    {
        return ItemResponseDTO::fromArray(
            $this->makeRequest(HttpMethod::PUT, "/items/{$itemId}", [], $data)
        );
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
    public function updateStatus(string $itemId, string $status): ItemResponseDTO
    {
        return $this->update($itemId, ['status' => $status]);
    }

    /**
     * Atualiza estoque e preco de uma variacao ou do item principal.
     */
    public function updatePriceAndStock(string $itemId, ?float $price = null, ?int $quantity = null): ItemResponseDTO
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

    // -----------------------------------------------------------------
    // Tipos de publicação (listing types / exposures)
    // -----------------------------------------------------------------

    /**
     * Tipos de publicação do site (GET /sites/{site}/listing_types).
     * Lista [{site_id, id (gold_pro, gold_special, free...), name}].
     */
    public function siteListingTypes(string $siteId = 'MLB'): array
    {
        return $this->makeRequest(HttpMethod::GET, '/sites/'.rawurlencode($siteId).'/listing_types');
    }

    /**
     * Detalhe de um tipo de publicação no site (GET /sites/{site}/listing_types/{type}):
     * configuration (exposição, duração, taxas), not_available_in_categories,
     * exceptions_by_category.
     */
    public function siteListingType(string $listingTypeId, string $siteId = 'MLB'): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            '/sites/'.rawurlencode($siteId).'/listing_types/'.rawurlencode($listingTypeId)
        );
    }

    /**
     * Níveis de exposição do site (GET /sites/{site}/listing_exposures):
     * lowest/low/mid/high/highest com home_page, priority_in_search etc.
     */
    public function listingExposures(string $siteId = 'MLB'): array
    {
        return $this->makeRequest(HttpMethod::GET, '/sites/'.rawurlencode($siteId).'/listing_exposures');
    }

    /**
     * Um nível de exposição específico (GET /sites/{site}/listing_exposures/{id}).
     */
    public function listingExposure(string $exposureId, string $siteId = 'MLB'): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            '/sites/'.rawurlencode($siteId).'/listing_exposures/'.rawurlencode($exposureId)
        );
    }

    /**
     * Tipos de publicação que o seller pode usar numa categoria
     * (GET /users/{id}/available_listing_types?category_id=...). Devolve
     * {category_id, available:[{id, name, remaining_listings}]}.
     */
    public function userAvailableListingTypes(int|string $userId, string $categoryId): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            "/users/{$userId}/available_listing_types",
            ['category_id' => $categoryId]
        );
    }

    /**
     * Motivo de um tipo de publicação estar (in)disponível para o seller numa
     * categoria (GET /users/{id}/available_listing_type/{type}?category_id=...).
     * Devolve {available, cause, code}.
     */
    public function userAvailableListingType(int|string $userId, string $listingTypeId, string $categoryId): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            "/users/{$userId}/available_listing_type/".rawurlencode($listingTypeId),
            ['category_id' => $categoryId]
        );
    }

    /**
     * Tipos de publicação disponíveis para um anúncio específico
     * (GET /items/{id}/available_listing_types).
     */
    public function availableListingTypes(string $itemId): array
    {
        return $this->makeRequest(HttpMethod::GET, '/items/'.rawurlencode($itemId).'/available_listing_types');
    }

    /**
     * Upgrades de exposição disponíveis (GET /items/{id}/available_upgrades).
     * O upgrade é permitido uma única vez por anúncio.
     */
    public function availableUpgrades(string $itemId): array
    {
        return $this->makeRequest(HttpMethod::GET, '/items/'.rawurlencode($itemId).'/available_upgrades');
    }

    /**
     * Downgrades disponíveis (GET /items/{id}/available_downgrades). Lista
     * vazia quando não há downgrade possível (anúncio free nunca tem).
     */
    public function availableDowngrades(string $itemId): array
    {
        return $this->makeRequest(HttpMethod::GET, '/items/'.rawurlencode($itemId).'/available_downgrades');
    }

    /**
     * Troca o tipo de publicação do anúncio (POST /items/{id}/listing_type
     * body {id: gold_special}). gold_special <-> gold_pro é livre; demais
     * dependem de availableUpgrades()/availableDowngrades(). Devolve o item.
     */
    public function changeListingType(string $itemId, string $listingTypeId): array
    {
        return $this->makeRequest(
            HttpMethod::POST,
            '/items/'.rawurlencode($itemId).'/listing_type',
            [],
            ['id' => $listingTypeId]
        );
    }

    // -----------------------------------------------------------------
    // Preços e custos (prices / PxQ / preços líquidos B2B)
    // -----------------------------------------------------------------

    /**
     * Todos os preços do anúncio (GET /items/{id}/prices) — standard, promo,
     * preço por quantidade e contextos B2B (user_type_business). Envia o
     * header `show-all-prices: true` (sem ele o ML esconde os preços PxQ) e
     * `display_version=true` para trazer a versão usada no X-Version dos POSTs.
     */
    public function prices(string $itemId, bool $showAllPrices = true, bool $displayVersion = true): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            '/items/'.rawurlencode($itemId).'/prices',
            $displayVersion ? ['display_version' => 'true'] : [],
            [],
            0,
            $showAllPrices ? ['show-all-prices' => 'true'] : []
        );
    }

    /**
     * Preço por quantidade em porcentagem (PxQ %) configurado no anúncio
     * (GET /items/{id}/prices/price-per-quantity).
     */
    public function pricePerQuantity(string $itemId): array
    {
        return $this->makeRequest(HttpMethod::GET, '/items/'.rawurlencode($itemId).'/prices/price-per-quantity');
    }

    /**
     * Grava (substitui) as faixas de PxQ % do anúncio
     * (POST /items/{id}/prices/price-per-quantity). Exige header `X-Version`
     * = versão atual do item (vem em prices(display_version=true)); versão
     * errada devolve 409. Lista vazia remove todas as faixas.
     * `$removeAbsolutePxq` envia remove-absolute-pxq=true para trocar um PxQ
     * absoluto (standard/quantity) pelo percentual numa operação só.
     *
     * @param  array<int, array<string,mixed>>  $pricePerQuantity  [{type: discount_percentage, percentage, conditions{context_restrictions, min_purchase_unit, max_purchase_unit}}]
     */
    public function setPricePerQuantity(string $itemId, array $pricePerQuantity, int|string $version, bool $removeAbsolutePxq = false): array
    {
        return $this->makeRequest(
            HttpMethod::POST,
            '/items/'.rawurlencode($itemId).'/prices/price-per-quantity',
            $removeAbsolutePxq ? ['remove-absolute-pxq' => 'true'] : [],
            ['price_per_quantity' => array_values($pricePerQuantity)],
            0,
            ['X-Version' => (string) $version]
        );
    }

    /**
     * Grava preços absolutos por quantidade / preço líquido B2B
     * (POST /items/{id}/prices/standard/quantity). Cada preço:
     * {type: standard, amount, currency_id, amount_tax_inclusion_type (net|gross),
     * conditions{context_restrictions, min_purchase_unit}}. Se já existe PxQ
     * percentual no mesmo contexto dá 400 — use `$removePercentagePxq`.
     *
     * @param  array<int, array<string,mixed>>  $prices
     */
    public function setStandardQuantityPrices(string $itemId, array $prices, bool $removePercentagePxq = false): array
    {
        return $this->makeRequest(
            HttpMethod::POST,
            '/items/'.rawurlencode($itemId).'/prices/standard/quantity',
            $removePercentagePxq ? ['remove_percentage_pxq' => 'true'] : [],
            ['prices' => array_values($prices)]
        );
    }

    /**
     * Elegibilidade do seller/anúncio para preços líquidos B2B
     * (GET /business/v1/sites/{site}/users/{user}/items/{item}/options/net-prices/seller/eligibility).
     */
    public function netPricesEligibility(int|string $userId, string $itemId, string $siteId = 'MLB'): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            '/business/v1/sites/'.rawurlencode($siteId)."/users/{$userId}/items/".rawurlencode($itemId).'/options/net-prices/seller/eligibility'
        );
    }

    /**
     * Recomendação de descontos PxQ % por faixa de quantidade
     * (POST /prices-per-quantity/v1/recommendations). Body:
     * {item_id, range_item_quantities:[2,5,10], price{standard_amount, currency}}.
     *
     * @param  array<int, int>  $rangeItemQuantities
     */
    public function pricePerQuantityRecommendations(string $itemId, array $rangeItemQuantities, float $standardAmount, string $currency = 'BRL'): array
    {
        return $this->makeRequest(HttpMethod::POST, '/prices-per-quantity/v1/recommendations', [], [
            'item_id' => $itemId,
            'range_item_quantities' => array_values($rangeItemQuantities),
            'price' => ['standard_amount' => $standardAmount, 'currency' => $currency],
        ]);
    }

    // -----------------------------------------------------------------
    // Catálogo (elegibilidade, competição, optin, forewarning)
    // -----------------------------------------------------------------

    /**
     * Elegibilidade do anúncio para virar publicação de catálogo
     * (GET /items/{id}/catalog_listing_eligibility): {status, buy_box_eligible,
     * reason, variations[{id, status, buy_box_eligible}]}.
     */
    public function catalogListingEligibility(string $itemId): array
    {
        return $this->makeRequest(HttpMethod::GET, '/items/'.rawurlencode($itemId).'/catalog_listing_eligibility');
    }

    /**
     * Elegibilidade em lote (GET /multiget/catalog_listing_eligibility?ids=A,B).
     * Máx. 20 ids por chamada.
     *
     * @param  array<int, string>  $itemIds
     */
    public function catalogListingEligibilityMultiGet(array $itemIds): array
    {
        if ($itemIds === []) {
            return [];
        }

        return $this->makeRequest(
            HttpMethod::GET,
            '/multiget/catalog_listing_eligibility',
            ['ids' => implode(',', array_slice(array_values($itemIds), 0, 20))]
        );
    }

    /**
     * Competição de catálogo (GET /items/{id}/price_to_win?version=v2):
     * status (winning|competing|sharing_first_place|listed), price_to_win,
     * boosts, winner. Só faz sentido para anúncio de catálogo.
     */
    public function priceToWin(string $itemId, string $version = 'v2'): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            '/items/'.rawurlencode($itemId).'/price_to_win',
            ['version' => $version]
        );
    }

    /**
     * Optin: cria a publicação de catálogo a partir de um anúncio de
     * marketplace (POST /items/catalog_listings). Body {item_id,
     * catalog_product_id, variation_id?}. Erros 44xx vêm no corpo.
     */
    public function createCatalogListing(string $itemId, string $catalogProductId, int|string|null $variationId = null): array
    {
        $body = ['item_id' => $itemId, 'catalog_product_id' => $catalogProductId];
        if ($variationId !== null) {
            $body['variation_id'] = $variationId;
        }

        return $this->makeRequest(HttpMethod::POST, '/items/catalog_listings', [], $body);
    }

    /**
     * Data-limite de moderação de um anúncio com tag catalog_forewarning
     * (GET /items/{id}/catalog_forewarning/date): {status: date_defined|
     * date_not_defined|date_expired, moderation_date}.
     */
    public function catalogForewarningDate(string $itemId): array
    {
        return $this->makeRequest(HttpMethod::GET, '/items/'.rawurlencode($itemId).'/catalog_forewarning/date');
    }

    // -----------------------------------------------------------------
    // User Products (migração de anúncio legado para user_product_listings)
    // -----------------------------------------------------------------

    /**
     * Valida se o anúncio pode migrar para o modelo de User Products
     * (GET /items/{id}/user_product_listings/validate).
     */
    public function validateUserProductListings(string $itemId): array
    {
        return $this->makeRequest(HttpMethod::GET, '/items/'.rawurlencode($itemId).'/user_product_listings/validate');
    }

    /**
     * Dispara a migração assíncrona do anúncio para User Products
     * (POST /sites/{site}/items/user_product_listings body {item_id}).
     * Acompanhe com migrationLiveListing().
     */
    public function migrateToUserProductListings(string $itemId, string $siteId = 'MLB'): array
    {
        return $this->makeRequest(
            HttpMethod::POST,
            '/sites/'.rawurlencode($siteId).'/items/user_product_listings',
            [],
            ['item_id' => $itemId]
        );
    }

    /**
     * Status da migração para User Products (GET /items/{id}/migration_live_listing):
     * variações e novos itens criados a partir do anúncio original.
     */
    public function migrationLiveListing(string $itemId): array
    {
        return $this->makeRequest(HttpMethod::GET, '/items/'.rawurlencode($itemId).'/migration_live_listing');
    }


    // ── Descrição (doc "Descrição de produtos") ───────────────────────────

    /**
     * Descrição do anúncio (GET /items/{id}/description): `{text, plain_text,
     * last_updated, date_created, snapshot}`. `text` (HTML legado) vem vazio
     * nos anúncios novos — use `plain_text`.
     *
     * @return array<string, mixed>
     */
    public function description(string $itemId): array
    {
        return $this->makeRequest(HttpMethod::GET, '/items/'.rawurlencode($itemId).'/description');
    }

    /**
     * Cria a descrição de um anúncio que ainda NÃO tem (POST /items/{id}/description).
     * Só texto plano, quebra de linha com "\n". Se o item já tem descrição
     * devolve 400 — nesse caso use updateDescription().
     *
     * @return array<string, mixed>
     */
    public function createDescription(string $itemId, string $plainText): array
    {
        return $this->makeRequest(
            HttpMethod::POST,
            '/items/'.rawurlencode($itemId).'/description',
            [],
            ['plain_text' => $plainText],
        );
    }

    /**
     * Substitui a descrição existente (PUT /items/{id}/description?api_version=2).
     * O `api_version=2` faz o erro de validação apontar a posição do caractere
     * inválido (emoji, tag HTML) no `cause`.
     *
     * @return array<string, mixed>
     */
    public function updateDescription(string $itemId, string $plainText): array
    {
        return $this->makeRequest(
            HttpMethod::PUT,
            '/items/'.rawurlencode($itemId).'/description',
            ['api_version' => 2],
            ['plain_text' => $plainText],
        );
    }

    // ── Variações (doc "Variações") ───────────────────────────────────────

    /**
     * Lista as variações do anúncio (GET /items/{id}/variations) — array
     * top-level com id, attribute_combinations, price, available_quantity,
     * sold_quantity, picture_ids, seller_custom_field, attributes.
     *
     * @return array<int, array<string, mixed>>
     */
    public function variations(string $itemId): array
    {
        return $this->makeRequest(HttpMethod::GET, '/items/'.rawurlencode($itemId).'/variations');
    }

    /**
     * Uma variação específica (GET /items/{id}/variations/{variationId}).
     *
     * @return array<string, mixed>
     */
    public function variation(string $itemId, int|string $variationId): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            '/items/'.rawurlencode($itemId).'/variations/'.rawurlencode((string) $variationId),
        );
    }

    /**
     * Adiciona UMA variação nova (POST /items/{id}/variations). Body:
     * `{attribute_combinations[{id|name, value_id|value_name}], price,
     * available_quantity, picture_ids[], attributes[]?, seller_custom_field?}`.
     * Devolve o item completo. Pra editar várias de uma vez use update() com
     * `variations[]` — e mande TODOS os ids, senão as ausentes são apagadas.
     *
     * @param  array<string, mixed>  $variation
     * @return array<string, mixed>
     */
    public function createVariation(string $itemId, array $variation): array
    {
        return $this->makeRequest(
            HttpMethod::POST,
            '/items/'.rawurlencode($itemId).'/variations',
            [],
            $variation,
        );
    }

    /**
     * Remove uma variação (DELETE /items/{id}/variations/{variationId}).
     * Devolve o item já sem ela.
     *
     * @return array<string, mixed>
     */
    public function deleteVariation(string $itemId, int|string $variationId): array
    {
        return $this->makeRequest(
            HttpMethod::DELETE,
            '/items/'.rawurlencode($itemId).'/variations/'.rawurlencode((string) $variationId),
        );
    }

    // ── Imagens (doc "Trabalhar com imagens") ─────────────────────────────

    /**
     * Sobe uma imagem pro CDN do ML (POST /pictures/items/upload, multipart
     * `file`) e devolve `{id, variations[{size,url,secure_url}], max_size}`.
     * O `id` é o picture_id que vai em `pictures[].id` do item/variação.
     * Só multipart — não aceita URL.
     *
     * @return array<string, mixed>
     */
    public function uploadPicture(string $contents, string $filename): array
    {
        $response = (clone $this->httpClient)
            ->attach('file', $contents, $filename)
            ->post('/pictures/items/upload');

        if ($response->failed()) {
            $this->handleError($response);
        }

        return $response->json() ?? [];
    }

    /**
     * Vincula uma imagem já hospedada ao anúncio (POST /items/{id}/pictures).
     * Body `{id}` (picture_id) ou `{source}` (URL pública). Anexa no FIM da
     * lista; pra reordenar/substituir use update() com `pictures[]`.
     *
     * @return array<string, mixed>
     */
    public function addPicture(string $itemId, ?string $pictureId = null, ?string $source = null): array
    {
        $body = array_filter(['id' => $pictureId, 'source' => $source], fn ($v) => $v !== null);

        return $this->makeRequest(
            HttpMethod::POST,
            '/items/'.rawurlencode($itemId).'/pictures',
            [],
            $body,
        );
    }

    /**
     * Por que uma imagem ficou em "Processando..." (GET /pictures/{id}/errors):
     * `{id, source, error{message, items}}` — tipicamente 403/404 no source
     * ou content-type que não bate com a extensão.
     *
     * @return array<string, mixed>
     */
    public function pictureErrors(string $pictureId): array
    {
        return $this->makeRequest(HttpMethod::GET, '/pictures/'.rawurlencode($pictureId).'/errors');
    }

    // ── Republicar / validar ──────────────────────────────────────────────

    /**
     * Republica um anúncio fechado (POST /items/{id}/relist) gerando um item
     * NOVO ligado ao pai. Body sem variações: `{price, quantity,
     * listing_type_id}`; com variações: `{listing_type_id, variations[{id,
     * price, quantity}]}`. Só UMA republicação por item pai; veículos/imóveis
     * até 60 dias após fechar.
     *
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function relist(string $itemId, array $body): array
    {
        return $this->makeRequest(HttpMethod::POST, '/items/'.rawurlencode($itemId).'/relist', [], $body);
    }

    /**
     * Valida o JSON de um anúncio SEM publicar (POST /items/validate) — mesmo
     * body do create(). 204 sem corpo quando está OK; 400 com `cause[]`
     * detalhando campo a campo quando não.
     *
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>
     */
    public function validate(array $item): array
    {
        return $this->makeRequest(HttpMethod::POST, '/items/validate', [], $item);
    }

    // ── Preços standard por canal (doc "API de preços") ───────────────────

    /**
     * Define preços standard por canal (POST /items/{id}/prices/standard).
     * Cada preço: `{conditions{context_restrictions: [channel_marketplace|
     * channel_mshops|...]}, amount, currency_id}`. Substitui os standard
     * existentes dos canais informados.
     *
     * @param  array<int, array<string, mixed>>  $prices
     * @return array<string, mixed>
     */
    public function setStandardPrices(string $itemId, array $prices): array
    {
        return $this->makeRequest(
            HttpMethod::POST,
            '/items/'.rawurlencode($itemId).'/prices/standard',
            [],
            ['prices' => array_values($prices)],
        );
    }

    // ── Kits virtuais (doc "Kits virtuais") ───────────────────────────────

    /**
     * Busca produtos do inventário elegíveis pra compor um kit
     * (POST /users/{sellerId}/kits/components/search?searchText&limit).
     * Body: `{active_channels: [marketplace], main_product_id?,
     * added_products?[], search_filters?{only_eligible: ONLY_ELIGIBLE,
     * family_id}}`. Paginação por `paging.search_after_hash`.
     *
     * @param  array<string, mixed>  $body
     * @param  array<string, mixed>  $query  searchText, limit, search_after
     * @return array<string, mixed>
     */
    public function searchKitComponents(int|string $sellerId, array $body, array $query = []): array
    {
        return $this->makeRequest(
            HttpMethod::POST,
            "/users/{$sellerId}/kits/components/search",
            $query,
            $body === [] ? ['active_channels' => ['marketplace']] : $body,
        );
    }

    /**
     * Cria um kit virtual (POST /items/kits) agrupando user products já
     * existentes. Body: `{family_name, channels[], thumbnail{id, secure_url},
     * price, currency_id, listing_type_id, official_store_id?, bundle{type:
     * kit, components[{type: user_product, user_product_id, quantity (<=10),
     * automatic_price: null | {discount}}]}}`. Kit tem de 2 a 6 componentes;
     * `discount` (quando usado) precisa ser igual em todos.
     *
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function createKit(array $body): array
    {
        return $this->makeRequest(HttpMethod::POST, '/items/kits', [], $body);
    }

    /**
     * Configuração de preço de um kit (GET /items/{id}/bundle/prices_configuration):
     * componentes com quantity e automatic_price.
     *
     * @return array<string, mixed>
     */
    public function kitPricesConfiguration(string $itemId): array
    {
        return $this->makeRequest(HttpMethod::GET, '/items/'.rawurlencode($itemId).'/bundle/prices_configuration');
    }

    /**
     * Liga/ajusta a sincronização de preço do kit
     * (PUT /items/{id}/bundle/prices_configuration). Body
     * `{bundle{components[{type: user_product, user_product_id,
     * automatic_price{discount}}]}}` — mesmo desconto em todos.
     *
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function updateKitPricesConfiguration(string $itemId, array $body): array
    {
        return $this->makeRequest(
            HttpMethod::PUT,
            '/items/'.rawurlencode($itemId).'/bundle/prices_configuration',
            [],
            $body,
        );
    }

    // ── Busca de itens do seller ──────────────────────────────────────────

    /**
     * Restrições da busca do seller (GET /users/{id}/items/search/restrictions):
     * `{aggregations_allowed, query_allowed, sort_allowed}`. Seller com mais
     * de 200 mil itens vem `aggregations_allowed=false` e o search() não
     * devolve `filters`/`available_filters`.
     *
     * @return array<string, mixed>
     */
    public function searchRestrictions(int|string $sellerId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/users/{$sellerId}/items/search/restrictions");
    }
}
