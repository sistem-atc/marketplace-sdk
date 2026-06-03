<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\Support;

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
        $baseString = $partnerId.$apiPath.$timestamp.$accessToken.$shopId;
        return hash_hmac('sha256', $baseString, $partnerKey);
    }
}
