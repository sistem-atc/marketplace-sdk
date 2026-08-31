<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\Endpoints\Seller;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Tiktok\Bases\BaseMethods;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Seller\ShopGroupData;

/**
 * Seller API do TikTok Shop (/seller/{version}/...) — dados do VENDEDOR, não
 * da loja. Nenhum dos dois endpoints usa shop_cipher: o escopo é o seller.
 */
class SellerMethods extends BaseMethods
{
    /**
     * Permissões cross-border do seller (`data.permissions[]`).
     *
     * Lista de strings crua (hoje só MANAGE_GLOBAL_PRODUCT). Lista VAZIA é
     * resposta válida e significa "sem permissão cross-border" — não é erro.
     *
     * @return list<string>
     */
    public function getPermissions(string $version = '202309'): array
    {
        $response = $this->makeRequest(HttpMethod::GET, "/seller/{$version}/permissions");

        return $response['data']['permissions'] ?? [];
    }

    /**
     * Grupo de interoperabilidade de produto do seller
     * (`data.shop_group_data`).
     *
     * É UM grupo, não uma lista: agrupa lojas de países diferentes que
     * compartilham catálogo (ex.: local US que abriu loja MX). Seller sem
     * grupo recebe o objeto vazio.
     */
    public function getShopGroups(string $version = '202601'): ShopGroupData
    {
        $response = $this->makeRequest(HttpMethod::GET, "/seller/{$version}/shop_groups");

        return ShopGroupData::fromArray($response['data']['shop_group_data'] ?? []);
    }
}
