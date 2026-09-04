<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\Endpoints\Fbs;

use SistemAtc\Marketplaces\Shopee\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;

/**
 * FBS (Fulfillment by Shopee) — consultas BR de elegibilidade e bloqueio.
 * As NFs FBS (generate/get/download_fbs_invoices) continuam em OrderMethods.
 */
class FbsMethods extends BaseMethods
{
    /** Se a loja esta habilitada no FBS BR (response.enrollment_status...). */
    public function queryBrShopEnrollmentStatus(): array
    {
        return $this->makeRequest(HttpMethod::GET, '/api/v2/fbs/query_br_shop_enrollment_status');
    }

    /**
     * Falhas de emissao de NF do FBS (inbound, RTS, venda, transferencia).
     * Paginacao por page_no/page_size (max 100).
     */
    public function queryBrShopInvoiceError(int $pageNo = 1, int $pageSize = 100): array
    {
        return $this->makeRequest(HttpMethod::GET, '/api/v2/fbs/query_br_shop_invoice_error', [
            'page_no' => $pageNo,
            'page_size' => min($pageSize, 100),
        ]);
    }

    /** Se a LOJA esta bloqueada no FBS BR (e motivo). */
    public function queryBrShopBlockStatus(): array
    {
        return $this->makeRequest(HttpMethod::GET, '/api/v2/fbs/query_br_shop_block_status');
    }

    /** Se um SKU (shop_sku_id) esta bloqueado no FBS BR. */
    public function queryBrSkuBlockStatus(string $shopSkuId): array
    {
        return $this->makeRequest(HttpMethod::GET, '/api/v2/fbs/query_br_sku_block_status', ['shop_sku_id' => $shopSkuId]);
    }
}
