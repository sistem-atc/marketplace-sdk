<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\Endpoints\PublicApi;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopee\Bases\BaseMethods;

/**
 * Public (api_type Public) — chamadas em nome do PARCEIRO (app), assinadas
 * so' com partner_id + path + timestamp (sem access_token nem shop_id).
 *
 * Nao cobre get_access_token nem refresh_access_token: o grant inicial ja'
 * vive em Support\OAuth::exchangeAuthorizationCode() e o refresh em
 * Support\TokenRefresher (ambos so' por shop_id hoje). Retorno bruto `array`.
 */
class PublicMethods extends BaseMethods
{
    private const BASE = '/api/v2/public/';

    /**
     * Lojas que autorizaram o app (authed_shop_list: shop_id, region, auth_time,
     * expire_time, sip_affi_shops). Paginacao page_no (1-based) + `more`.
     */
    public function getShopsByPartner(int $pageNo = 1, int $pageSize = 100): array
    {
        return $this->makeRequest(HttpMethod::GET, self::BASE.'get_shops_by_partner', [
            'page_no' => $pageNo,
            'page_size' => $pageSize,
        ], [], true);
    }

    /** Merchants (contas cross-border) que autorizaram o app. Paginacao page_no + `more`. */
    public function getMerchantsByPartner(int $pageNo = 1, int $pageSize = 100): array
    {
        return $this->makeRequest(HttpMethod::GET, self::BASE.'get_merchants_by_partner', [
            'page_no' => $pageNo,
            'page_size' => $pageSize,
        ], [], true);
    }

    /**
     * Troca o resend_code (gerado pelo vendedor em "Shop Authorization
     * Management" -> reenviar) por access_token + refresh_token. Uso unico,
     * expira em 10 min. NAO persiste — o host salva.
     */
    public function getTokenByResendCode(string $resendCode): array
    {
        return $this->makeRequest(HttpMethod::POST, self::BASE.'get_token_by_resend_code', [], [
            'resend_code' => $resendCode,
        ], true);
    }

    /** Faixas de IP de onde a Shopee dispara webhooks/push — pra allowlist no firewall. */
    public function getShopeeIpRanges(): array
    {
        return $this->makeRequest(HttpMethod::GET, self::BASE.'get_shopee_ip_ranges', [], [], true);
    }
}
