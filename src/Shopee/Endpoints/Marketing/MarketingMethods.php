<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\Endpoints\Marketing;

use SistemAtc\Marketplaces\Shopee\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;

class MarketingMethods extends BaseMethods
{
    /**
     * Lista descontos/promocoes criadas pelo vendedor.
     *
     * Modulo real da Shopee: /api/v2/discount (param discount_status,
     * paginacao por page_no 1-based). O antigo /api/v2/marketing nao existe
     * (retorna error_not_found).
     */
    public function getDiscountList(string $status, int $pageNo = 1, int $pageSize = 100): array
    {
        return $this->makeRequest(HttpMethod::GET, '/api/v2/discount/get_discount_list', [
            'discount_status' => $status,
            'page_no' => $pageNo,
            'page_size' => $pageSize,
        ]);
    }

    /**
     * Detalhes de um desconto especifico — /api/v2/discount/get_discount.
     */
    public function getDiscount(int $discountId): array
    {
        return $this->makeRequest(HttpMethod::GET, '/api/v2/discount/get_discount', [
            'discount_id' => $discountId,
        ]);
    }

    /**
     * Lista cupons (Vouchers) do vendedor — /api/v2/voucher/get_voucher_list
     * (param status, paginacao por page_no 1-based).
     */
    public function getVoucherList(string $status, int $pageNo = 1, int $pageSize = 100): array
    {
        return $this->makeRequest(HttpMethod::GET, '/api/v2/voucher/get_voucher_list', [
            'status' => $status,
            'page_no' => $pageNo,
            'page_size' => $pageSize,
        ]);
    }
}
