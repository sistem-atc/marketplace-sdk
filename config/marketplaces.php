<?php

return [
    'mercadolivre' => [
        'api_base' => env('MARKETPLACES_ML_BASE_URL', 'https://api.mercadolivre.com'),
        'webhook_secret' => env('MARKETPLACES_ML_WEBHOOK_SECRET'),
    ],
    'shopee' => [
        'base_url' => env('MARKETPLACES_SHOPEE_BASE_URL', 'https://partner.shopeemobile.com'),
        'webhook_secret' => env('MARKETPLACES_SHOPEE_WEBHOOK_SECRET'),
    ],
    'magalu' => [
        'api_base' => env('MARKETPLACES_MAGALU_BASE_URL', 'https://api.magalu.com'),
        'token_url' => env('MARKETPLACES_MAGALU_TOKEN_URL', 'https://autoseg-idp.luizalabs.com/oauth/token'),
    ],
    'tiktok' => [
        'base_url' => env('MARKETPLACES_TIKTOK_BASE_URL', 'https://open-api.tiktokglobalshop.com'),
    ],
    // Marketing API do TikTok for Business (ads/GMV Max) — host e auth
    // próprios, separados do TikTok Shop acima.
    'tiktok_ads' => [
        'base_url' => env('MARKETPLACES_TIKTOK_ADS_BASE_URL', 'https://business-api.tiktok.com'),
    ],
    'amazon' => [
        'spapi_base_url' => env('MARKETPLACES_AMAZON_BASE_URL', 'https://sellingpartnerapi-na.amazon.com'),
        'lwa_token_url' => env('MARKETPLACES_AMAZON_LWA_TOKEN_URL', 'https://api.amazon.com/auth/o2/token'),
    ],
    // Amazon Ads API (host próprio; região NA cobre o BR) — separada da SP-API.
    'amazon_ads' => [
        'base_url' => env('MARKETPLACES_AMAZON_ADS_BASE_URL', 'https://advertising-api.amazon.com'),
    ],
    'shopify' => [
        'api_version' => env('MARKETPLACES_SHOPIFY_API_VERSION', '2024-04'),
    ],
    'mercadopago' => [
        'api_base' => env('MARKETPLACES_MERCADOPAGO_BASE_URL', 'https://api.mercadopago.com'),
    ],
    'lojaintegrada' => [
        'api_base' => env('MARKETPLACES_LOJAINTEGRADA_BASE_URL', 'https://api.awsli.com.br/v1'),
    ],
    'netshoes' => [
        // PROD usa HTTPS; HOMOLOG (sandbox) e' HTTP (https reseta a conexao).
        'api_base' => env('MARKETPLACES_NETSHOES_BASE_URL', 'https://api-marketplace.netshoes.com.br'),
        'sandbox_base' => env('MARKETPLACES_NETSHOES_SANDBOX_URL', 'http://api-sandbox.netshoes.com.br'),
    ],
    // Conecta La (plataforma do "Shophub"). Auth por HEADER (x-api-key,
    // x-store-key, x-provider-key, x-user-email...). HOMOLOG = teste.conectala.
    'conectala' => [
        'api_base' => env('MARKETPLACES_CONECTALA_BASE_URL', 'http://teste.conectala.com.br/app/Api/V1'),
        'sellercenter_base' => env('MARKETPLACES_CONECTALA_SELLERCENTER_URL', 'http://teste.conectala.com.br/app'),
    ],
    'webhooks' => [
        // Catch-all legado (api/webhooks/{marketplace}). So' usar quando NAO
        // houver lista per-MP em `routes`. Default false — preferir `routes`.
        'enabled' => false,
        'prefix' => env('MARKETPLACES_WEBHOOK_PREFIX', 'api/webhooks'),
        'middleware' => ['api'],
        // Modo per-MP: lista de marketplaces cujas rotas o pacote registra
        // (api/webhooks/{mp}). Migracao um-a-um — o host so' adiciona o MP aqui
        // quando o listener/validacao dele ja' estao prontos.
        'routes' => [],
        'tenant_routes' => [],
    ],
];
