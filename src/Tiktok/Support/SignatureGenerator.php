<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\Support;

class SignatureGenerator
{
    public static function sign(
        string $apiPath,
        array $queryParams,
        ?string $bodyJson,
        string $appSecret,
    ): string {
        $filtered = array_filter(
            $queryParams,
            fn ($key) => ! in_array($key, ['sign', 'access_token'], true),
            ARRAY_FILTER_USE_KEY,
        );
        ksort($filtered);

        $concatenated = '';
        foreach ($filtered as $key => $value) {
            $concatenated .= $key . self::stringifyValue($value);
        }

        $baseString = $apiPath . $concatenated;
        if ($bodyJson !== null && $bodyJson !== '') {
            $baseString .= $bodyJson;
        }

        $wrapped = $appSecret . $baseString . $appSecret;
        return hash_hmac('sha256', $wrapped, $appSecret);
    }

    private static function stringifyValue(mixed $value): string
    {
        if (is_bool($value)) return $value ? 'true' : 'false';
        if (is_null($value)) return '';
        if (is_array($value)) return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '';
        return (string) $value;
    }
}
