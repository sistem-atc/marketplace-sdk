<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\Support;

/**
 * HMAC-SHA256 das chamadas Shopee v2.
 *
 * Base string das APIs autenticadas: partner_id + path + timestamp +
 * access_token + <id da entidade>. A entidade muda por api_type (shop_id,
 * merchant_id, principal_id, user_id) mas a formula e' a mesma — shopSign()
 * e merchantSign() sao atalhos de entitySign().
 */
class SignatureGenerator
{
    public static function publicSign(
        int $partnerId,
        string $apiPath,
        int $timestamp,
        string $partnerKey,
    ): string {
        $baseString = $partnerId.$apiPath.$timestamp;
        return hash_hmac('sha256', $baseString, $partnerKey);
    }

    public static function shopSign(
        int $partnerId,
        string $apiPath,
        int $timestamp,
        string $accessToken,
        int $shopId,
        string $partnerKey,
    ): string {
        return self::entitySign($partnerId, $apiPath, $timestamp, $accessToken, $shopId, $partnerKey);
    }

    /** APIs api_type "Merchant" (global_product, merchant): merchant_id no lugar do shop_id. */
    public static function merchantSign(
        int $partnerId,
        string $apiPath,
        int $timestamp,
        string $accessToken,
        int $merchantId,
        string $partnerKey,
    ): string {
        return self::entitySign($partnerId, $apiPath, $timestamp, $accessToken, $merchantId, $partnerKey);
    }

    /** Formula generica: entityId = shop_id | merchant_id | principal_id | user_id. */
    public static function entitySign(
        int $partnerId,
        string $apiPath,
        int $timestamp,
        string $accessToken,
        int $entityId,
        string $partnerKey,
    ): string {
        $baseString = $partnerId.$apiPath.$timestamp.$accessToken.$entityId;
        return hash_hmac('sha256', $baseString, $partnerKey);
    }
}
