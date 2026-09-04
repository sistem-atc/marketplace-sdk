<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\Endpoints\Shop;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopee\Bases\BaseMethods;

/**
 * Módulo `/api/v2/shop` — cadastro da loja: perfil, armazéns, notificações do
 * Seller Center, marcas autorizadas, onboarding fiscal (BR) e modo férias.
 *
 * Todas as rotas são Shop (assinatura com access_token + shop_id).
 */
class ShopMethods extends BaseMethods
{
    /**
     * Dados básicos da loja: shop_name, region, status, sip_affi_shops, is_cb,
     * is_cnsc, auth_time, expire_time (validade da autorização do app), shop_cbil…
     *
     * A resposta vem no ROOT do body (não em `response`), por isso devolvemos o
     * body inteiro.
     *
     * @return array<string, mixed>
     */
    public function getShopInfo(): array
    {
        return $this->makeRequest(HttpMethod::GET, '/api/v2/shop/get_shop_info');
    }

    /**
     * Perfil público da loja: shop_logo, description, shop_name, invoice_issuer.
     *
     * @return array<string, mixed>
     */
    public function getProfile(): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/shop/get_profile');

        return $response['response'] ?? [];
    }

    /**
     * Atualiza nome, logo (URL de imagem já hospedada na Shopee) e/ou descrição.
     * Só os campos informados (não-null) vão no body.
     *
     * @return array<string, mixed>  shop_logo, description, shop_name aplicados
     */
    public function updateProfile(?string $shopName = null, ?string $shopLogo = null, ?string $description = null): array
    {
        $body = array_filter([
            'shop_name' => $shopName,
            'shop_logo' => $shopLogo,
            'description' => $description,
        ], static fn ($v) => $v !== null);

        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/shop/update_profile', [], $body);

        return $response['response'] ?? [];
    }

    /**
     * Armazéns da loja (todos numa chamada): warehouse_id, warehouse_name,
     * location_id, address_id, region/state/city/address/zipcode.
     * `warehouseType` 1 = Pickup (default), 2 = Return.
     *
     * A lista vem direto em `response` (array de armazéns).
     *
     * @return list<array<string, mixed>>
     */
    public function getWarehouseDetail(?int $warehouseType = null): array
    {
        $query = $warehouseType === null ? [] : ['warehouse_type' => $warehouseType];
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/shop/get_warehouse_detail', $query);

        return $response['response'] ?? [];
    }

    /**
     * Notificações do Seller Center (permissão depende do tipo do app).
     * Paginação por cursor = último `notification_id` devolvido; page_size
     * default 10, máx 50.
     *
     * @return array<string, mixed>  cursor, data[]
     */
    public function getShopNotification(?int $cursor = null, int $pageSize = 10): array
    {
        $query = ['page_size' => $pageSize];
        if ($cursor !== null) {
            $query['cursor'] = $cursor;
        }

        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/shop/get_shop_notification', $query);

        return $response['response'] ?? [];
    }

    /**
     * Marcas para as quais a loja é revendedora autorizada (Authorised Reseller).
     * Paginação por page_no (1-based) + page_size.
     *
     * @return array<string, mixed>  is_authorised_reseller, total_count, more, authorised_brand_list[]
     */
    public function getAuthorisedResellerBrand(int $pageNo = 1, int $pageSize = 50): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/shop/get_authorised_reseller_brand', [
            'page_no' => $pageNo,
            'page_size' => $pageSize,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * [Só BR] KYC/onboarding fiscal da loja: tax_id_type, cpf_id/cnpj_id, nome,
     * razão social, inscrição estadual, billing_address, onboarding_status.
     * Útil para casar a loja Shopee com a company (CNPJ) do ERP.
     *
     * @return array<string, mixed>
     */
    public function getBrShopOnboardingInfo(): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/shop/get_br_shop_onboarding_info');

        return $response['response'] ?? [];
    }

    /**
     * Modo férias: se está ligado, tipo (0 = total, 1 = parcial) e a janela
     * agendada (holiday_mode_start_time/end_time, unix).
     *
     * @return array<string, mixed>
     */
    public function getShopHolidayMode(): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/shop/get_shop_holiday_mode');

        return $response['response'] ?? [];
    }

    /**
     * Liga/desliga ou agenda o modo férias. Tipo 0 = total (sem novos pedidos),
     * 1 = parcial. Start deve ser hora cheia; end+1s deve ser hora cheia
     * (ex.: 17:59:59). Sem start/end aplica imediatamente.
     *
     * @return array<string, mixed>  debug_msg
     */
    public function setShopHolidayMode(
        bool $holidayModeOn,
        ?int $holidayModeType = null,
        ?int $holidayModeStartTime = null,
        ?int $holidayModeEndTime = null,
        ?string $holidayModeDescription = null,
    ): array {
        $body = ['holiday_mode_on' => $holidayModeOn];
        foreach ([
            'holiday_mode_type' => $holidayModeType,
            'holiday_mode_start_time' => $holidayModeStartTime,
            'holiday_mode_end_time' => $holidayModeEndTime,
            'holiday_mode_description' => $holidayModeDescription,
        ] as $k => $v) {
            if ($v !== null) {
                $body[$k] = $v;
            }
        }

        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/shop/set_shop_holiday_mode', [], $body);

        return $response['response'] ?? [];
    }
}
