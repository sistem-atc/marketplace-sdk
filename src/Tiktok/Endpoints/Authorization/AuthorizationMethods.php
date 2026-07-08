<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\Endpoints\Authorization;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Tiktok\Bases\BaseMethods;

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
     * atual. Retorna data.shops[] com { id, name, region, seller_type,
     * cipher (=shop_cipher), code }.
     *
     * @return array<string, mixed>
     */
    public function getAuthorizedShops(): array
    {
        return $this->makeRequest(HttpMethod::GET, '/authorization/202309/shops');
    }
}
