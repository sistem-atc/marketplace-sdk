<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\Endpoints\Authorization;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Tiktok\Bases\BaseMethods;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Authorization\AuthorizedShop;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Authorization\CategoryAsset;

/**
 * Authorization API do TikTok Shop.
 *
 * Usada logo apos o OAuth (OAuth::exchangeAuthorizationCode) pra descobrir as
 * lojas que o seller autorizou + o `shop_cipher` de cada uma — que e'
 * necessario em TODA chamada de negocio (orders/finance/...). O shop_cipher
 * NAO vem no token/get; so' aqui.
 */
class AuthorizationMethods extends BaseMethods
{
    /**
     * GET /authorization/202309/shops — lojas autorizadas pro access_token
     * atual. Desembrulha `data.shops[]` em DTOs tipados.
     *
     * @return list<AuthorizedShop>
     */
    public function getAuthorizedShops(): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/authorization/202309/shops');

        return array_map(
            static fn (array $s): AuthorizedShop => AuthorizedShop::fromArray($s),
            $response['data']['shops'] ?? [],
        );
    }
    /**
     * GET /authorization/202405/category_assets — categorias de negócio que o
     * PARCEIRO autorizou pro app (`data.category_assets[]`).
     *
     * Não confundir com o getAuthorizedShops: aqui o token é de PARCEIRO
     * (user_type=3, escopo partner.authorization.info) e o `cipher` devolvido
     * identifica o parceiro (prefixo TTP_), não uma loja — usar esse cipher
     * como shop_cipher devolve resposta errada, não erro.
     *
     * Compare a categoria por `category->id`; o `name` muda com o tempo.
     *
     * @return list<CategoryAsset>
     */
    public function getAuthorizedCategoryAssets(string $version = '202405'): array
    {
        $response = $this->makeRequest(HttpMethod::GET, "/authorization/{$version}/category_assets");

        return array_map(
            static fn (array $a): CategoryAsset => CategoryAsset::fromArray($a),
            $response['data']['category_assets'] ?? [],
        );
    }
}
