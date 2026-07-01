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
    'amazon' => [
        'spapi_base_url' => env('MARKETPLACES_AMAZON_BASE_URL', 'https://sellingpartnerapi-na.amazon.com'),
        'lwa_token_url' => env('MARKETPLACES_AMAZON_LWA_TOKEN_URL', 'https://api.amazon.com/auth/o2/token'),
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
