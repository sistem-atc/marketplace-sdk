<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\Endpoints\Merchant;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopee\Bases\BaseMethods;

/**
 * Merchant (api_type Merchant) — conta-mae cross-border (CNSC/KRSC) que agrupa
 * as lojas locais. Assina com merchant_id (settings['merchant_id']) e exige
 * access_token autorizado pelo merchant. Retorno bruto `array`.
 */
class MerchantMethods extends BaseMethods
{
    private const BASE = '/api/v2/merchant/';

    /** Nome, regiao, moeda e status (is_cnsc / is_upgraded_cbsc) do merchant. */
    public function getMerchantInfo(): array
    {
        return $this->makeRequest(HttpMethod::GET, self::BASE.'get_merchant_info', [], [], false, 0, true);
    }

    /** Lojas vinculadas ao merchant (shop_id + region + sip_affi_shops). Paginacao page_no (1-based). */
    public function getShopListByMerchant(int $pageNo = 1, int $pageSize = 100): array
    {
        return $this->makeRequest(HttpMethod::GET, self::BASE.'get_shop_list_by_merchant', [
            'page_no' => $pageNo,
            'page_size' => $pageSize,
        ], [], false, 0, true);
    }

    /** Locations (location_id) de estoque do merchant — obrigatorio no seller_stock de merchant com loja 3PF. */
    public function getMerchantWarehouseLocationList(): array
    {
        return $this->makeRequest(HttpMethod::GET, self::BASE.'get_merchant_warehouse_location_list', [], [], false, 0, true);
    }

    /**
     * Armazens do merchant. warehouse_type 1=pickup, 2=return. Cursor
     * bidirecional: 1a pagina next_id=0/prev_id=0; depois repasse o cursor
     * da resposta. page_size [1,30].
     */
    public function getMerchantWarehouseList(int $warehouseType = 1, int $pageSize = 30, int $nextId = 0, int $prevId = 0): array
    {
        return $this->makeRequest(HttpMethod::POST, self::BASE.'get_merchant_warehouse_list', [], [
            'warehouse_type' => $warehouseType,
            'cursor' => ['next_id' => $nextId, 'prev_id' => $prevId, 'page_size' => $pageSize],
        ], false, 0, true);
    }

    /** Lojas elegiveis a usar um armazem. Mesma paginacao bidirecional de getMerchantWarehouseList. */
    public function getWarehouseEligibleShopList(int $warehouseId, int $warehouseType = 1, int $pageSize = 30, int $nextId = 0, int $prevId = 0): array
    {
        return $this->makeRequest(HttpMethod::POST, self::BASE.'get_warehouse_eligible_shop_list', [], [
            'warehouse_id' => $warehouseId,
            'warehouse_type' => $warehouseType,
            'cursor' => ['next_id' => $nextId, 'prev_id' => $prevId, 'page_size' => $pageSize],
        ], false, 0, true);
    }

    /** Contas pre-pagas (prepaid account) do merchant. Paginacao page_no (1-based). */
    public function getMerchantPrepaidAccountList(int $pageNo = 1, int $pageSize = 100): array
    {
        return $this->makeRequest(HttpMethod::GET, self::BASE.'get_merchant_prepaid_account_list', [
            'page_no' => $pageNo,
            'page_size' => $pageSize,
        ], [], false, 0, true);
    }
}
