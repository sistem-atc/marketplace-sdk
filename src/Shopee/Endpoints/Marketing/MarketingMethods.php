<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\Endpoints\Marketing;

use SistemAtc\Marketplaces\Shopee\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;

class MarketingMethods extends BaseMethods
{
    /**
     * Lista descontos/promocoes criadas pelo vendedor.
     */
    public function getDiscountList(string $status, int $offset = 0, int $pageSize = 20): array
    {
        return $this->makeRequest(HttpMethod::GET, '/api/v2/marketing/get_discount_list', [
            'status' => $status,
            'offset' => $offset,
            'page_size' => $pageSize,
        ]);
    }

    /**
     * Detalhes de um desconto especifico.
     */
    public function getDiscount(int $discountId): array
    {
        return $this->makeRequest(HttpMethod::GET, '/api/v2/marketing/get_discount', [
            'discount_id' => $discountId,
        ]);
    }

    /**
     * Lista cupons (Vouchers) do vendedor.
     */
    public function getVoucherList(string $status, int $offset = 0, int $pageSize = 20): array
    {
        return $this->makeRequest(HttpMethod::GET, '/api/v2/marketing/get_voucher_list', [
            'status' => $status,
            'offset' => $offset,
            'page_size' => $pageSize,
        ]);
    }
}
