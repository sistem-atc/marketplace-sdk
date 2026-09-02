<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\Endpoints\UserProduct;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\MercadoLivre\Bases\BaseMethods;

/**
 * User Products (MLBU...): famílias, produtos do seller, estoque distribuído
 * por depósito (multi-origem) e lojas/depósitos do seller.
 */
class UserProductMethods extends BaseMethods
{
    // -----------------------------------------------------------------
    // User product
    // -----------------------------------------------------------------

    /**
     * Detalhe de um User Product (GET /user-products/{id}): family_id,
     * attributes, pictures, domain_id, e o item associado.
     */
    public function get(string $userProductId): array
    {
        return $this->makeRequest(HttpMethod::GET, '/user-products/'.rawurlencode($userProductId));
    }

    /**
     * Cria um anúncio a partir de um User Product existente
     * (POST /user-products/{id}/items). Obrigatórios no body: price,
     * category_id, currency_id, buying_mode, listing_type_id. Devolve 201
     * com o item criado.
     *
     * @param  array<string,mixed>  $body
     */
    public function createItem(string $userProductId, array $body): array
    {
        return $this->makeRequest(HttpMethod::POST, '/user-products/'.rawurlencode($userProductId).'/items', [], $body);
    }

    // -----------------------------------------------------------------
    // Famílias
    // -----------------------------------------------------------------

    /**
     * Família de User Products (GET /user-products-families/{id}):
     * family_name, domain_id, attributes comuns.
     */
    public function family(int|string $familyId): array
    {
        return $this->makeRequest(HttpMethod::GET, '/user-products-families/'.rawurlencode((string) $familyId));
    }

    /**
     * Família no contexto de um site (GET /sites/{site}/user-products-families/{id}).
     */
    public function siteFamily(int|string $familyId, string $siteId = 'MLB'): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            '/sites/'.rawurlencode($siteId).'/user-products-families/'.rawurlencode((string) $familyId)
        );
    }

    /**
     * Atualiza dados comuns da família (PUT /user-products-families/{id}),
     * ex.: {family_name, attributes:[{id, values:[{id}]}]}.
     *
     * @param  array<string,mixed>  $body
     */
    public function updateFamily(int|string $familyId, array $body): array
    {
        return $this->makeRequest(HttpMethod::PUT, '/user-products-families/'.rawurlencode((string) $familyId), [], $body);
    }

    /**
     * User Products da família (GET /user-products-families/{id}/user-products).
     */
    public function familyUserProducts(int|string $familyId): array
    {
        return $this->makeRequest(HttpMethod::GET, '/user-products-families/'.rawurlencode((string) $familyId).'/user-products');
    }

    /**
     * Adiciona um User Product (variação) à família
     * (POST /user-products-families/{id}/user-products). Body {attributes,
     * pictures, main_features}.
     *
     * @param  array<string,mixed>  $body
     */
    public function addFamilyUserProduct(int|string $familyId, array $body): array
    {
        return $this->makeRequest(
            HttpMethod::POST,
            '/user-products-families/'.rawurlencode((string) $familyId).'/user-products',
            [],
            $body
        );
    }

    /**
     * Atualiza em lote os User Products da família
     * (PUT /user-products-families/{id}/user-products body {user_products:[{id, attributes}]}).
     * Assíncrono: devolve task; acompanhe com familyTask().
     *
     * @param  array<int, array<string,mixed>>  $userProducts
     */
    public function updateFamilyUserProducts(int|string $familyId, array $userProducts): array
    {
        return $this->makeRequest(
            HttpMethod::PUT,
            '/user-products-families/'.rawurlencode((string) $familyId).'/user-products',
            [],
            ['user_products' => array_values($userProducts)]
        );
    }

    /**
     * Cria uma task de alteração da família (POST /user-products-families/{id}/tasks
     * body {common_content{family_name, domain_id, attributes}}). Devolve {id, status}.
     *
     * @param  array<string,mixed>  $body
     */
    public function createFamilyTask(int|string $familyId, array $body): array
    {
        return $this->makeRequest(HttpMethod::POST, '/user-products-families/'.rawurlencode((string) $familyId).'/tasks', [], $body);
    }

    /**
     * Status de uma task de família (GET /user-products-families/tasks/{taskId}).
     */
    public function familyTask(int|string $taskId): array
    {
        return $this->makeRequest(HttpMethod::GET, '/user-products-families/tasks/'.rawurlencode((string) $taskId));
    }

    // -----------------------------------------------------------------
    // Estoque distribuído / multi-origem
    // -----------------------------------------------------------------

    /**
     * Estoque por localização do User Product (GET /user-products/{id}/stock):
     * {locations[{type: selling_address|meli_facility|seller_warehouse, store_id?,
     * network_node_id?, quantity}], user_id, id}. Se precisar do header
     * `x-version` (obrigatório no PUT) use stockWithVersion().
     */
    public function stock(string $userProductId): array
    {
        return $this->makeRequest(HttpMethod::GET, '/user-products/'.rawurlencode($userProductId).'/stock');
    }

    /**
     * Mesmo que stock(), mas devolve também a versão do estoque que vem no
     * header `x-version` da resposta — é ela que o PUT seller_warehouse exige
     * (sem header = 400; versão velha = 409).
     *
     * @return array{stock: array<string,mixed>, version: ?string}
     */
    public function stockWithVersion(string $userProductId): array
    {
        $response = $this->httpClient->get('/user-products/'.rawurlencode($userProductId).'/stock');

        if ($response->failed()) {
            $this->handleError($response);
        }

        return [
            'stock' => $response->json() ?? [],
            'version' => $response->header('x-version') ?: null,
        ];
    }

    /**
     * Ajusta o estoque por depósito (PUT /user-products/{id}/stock/type/seller_warehouse).
     * Body {locations:[{store_id, network_node_id, quantity}]}; header
     * `x-version` = versão atual (stockWithVersion()). Seller de depósito
     * único só pode usar um network_node_id.
     *
     * @param  array<int, array<string,mixed>>  $locations
     */
    public function updateSellerWarehouseStock(string $userProductId, array $locations, int|string $version): array
    {
        return $this->makeRequest(
            HttpMethod::PUT,
            '/user-products/'.rawurlencode($userProductId).'/stock/type/seller_warehouse',
            [],
            ['locations' => array_values($locations)],
            0,
            ['x-version' => (string) $version]
        );
    }

    /**
     * Lojas/depósitos do seller (GET /users/{id}/stores/search?tags=stock_location).
     * Traz store_id e network_node_id usados nas locations de estoque.
     *
     * @param  array<string,mixed>  $query
     */
    public function searchStores(int|string $userId, array $query = ['tags' => 'stock_location']): array
    {
        return $this->makeRequest(HttpMethod::GET, "/users/{$userId}/stores/search", $query);
    }

    /**
     * Cria um anúncio já com estoque distribuído por depósito
     * (POST /items/multiwarehouse). Mesmo body de POST /items mais
     * `channels` e `stock_locations:[{store_id, network_node_id, quantity}]`.
     *
     * @param  array<string,mixed>  $body
     */
    public function createMultiwarehouseItem(array $body): array
    {
        return $this->makeRequest(HttpMethod::POST, '/items/multiwarehouse', [], $body);
    }
}
