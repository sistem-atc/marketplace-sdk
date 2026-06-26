<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\Endpoints\Product;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Magalu\Bases\BaseMethods;

/**
 * Endpoints de PORTFÓLIO (catálogo) da Seller API Magalu.
 *
 * Rotas reais do catálogo (confirmadas contra api.magalu.com em 2026-06-26) — o
 * antigo `/seller/v1/items` do ProductMethods responde 404 `resource_not_found`:
 *
 *   - GET /seller/v1/portfolios/skus         → lista paginada de SKUs (envelope
 *     `results`, paginação por `_limit`/`_offset`). NÃO traz preço.
 *   - GET /seller/v1/portfolios/prices/{sku} → preço do SKU (envelope `results`
 *     com `list_price` = DE, `price` = PARA, `normalizer` = divisor; ex.: 5990 /
 *     100 = R$ 59,90).
 *   - GET /seller/v1/portfolios/stocks/{sku} → estoque (exige escopo
 *     `open:portfolio-stocks-seller:read`).
 */
class PortfolioMethods extends BaseMethods
{
    /**
     * Uma página do catálogo de SKUs do seller (envelope `results`).
     *
     * @return array<string, mixed>
     */
    public function listSkus(int $limit = 50, int $offset = 0): array
    {
        return $this->makeRequest(HttpMethod::GET, '/seller/v1/portfolios/skus', [
            '_limit' => $limit,
            '_offset' => $offset,
        ]);
    }

    /**
     * Preço de um SKU (envelope `results` com list_price/price/normalizer).
     *
     * @return array<string, mixed>
     */
    public function priceBySku(string $sku): array
    {
        return $this->makeRequest(HttpMethod::GET, '/seller/v1/portfolios/prices/'.rawurlencode($sku));
    }

    /**
     * Estoque de um SKU (exige escopo open:portfolio-stocks-seller:read).
     *
     * @return array<string, mixed>
     */
    public function stockBySku(string $sku): array
    {
        return $this->makeRequest(HttpMethod::GET, '/seller/v1/portfolios/stocks/'.rawurlencode($sku));
    }
}
