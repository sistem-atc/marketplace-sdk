<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\ConectaLa\Endpoints\Product;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\ConectaLa\Bases\BaseMethods;

/** Variações de produto (Variants). Path da API = `/Variations`. */
class VariantMethods extends BaseMethods
{
    /** Variações de um produto (GET /Variations/{sku}). */
    public function get(string $sku): array
    {
        return $this->makeRequest(HttpMethod::GET, "/Variations/{$sku}");
    }

    /** Cria variações do produto (POST /Variations/{sku}). */
    public function create(string $sku, array $body): array
    {
        return $this->makeRequest(HttpMethod::POST, "/Variations/{$sku}", body: $body);
    }

    /** Atualiza as variações do produto (PUT /Variations/{sku}). */
    public function update(string $sku, array $body): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/Variations/{$sku}", body: $body);
    }

    /** Atualiza UMA variação específica (PUT /Variations/sku/{prodSku}/{varSku}). */
    public function updateOne(string $productSku, string $variantSku, array $body): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/Variations/sku/{$productSku}/{$variantSku}", body: $body);
    }
}
