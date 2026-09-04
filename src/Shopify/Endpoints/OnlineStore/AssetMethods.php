<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\Endpoints\OnlineStore;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopify\Bases\BaseMethods;

/**
 * Assets (arquivos) de um tema da loja online.
 *
 * Recurso REST: `asset`. A chave do asset (ex.: `templates/index.liquid`)
 * vai na query `asset[key]`, nao no path.
 * Obs.: a partir da versao 2024-01 so' apps com escopo `write_themes` e
 * o Theme Access app conseguem escrever assets.
 */
class AssetMethods extends BaseMethods
{
    /**
     * Lista os assets de um tema (metadados, sem conteudo).
     *
     * @param  array<string, mixed>  $params  fields
     */
    public function list(int|string $themeId, array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, "/themes/{$themeId}/assets", $params);
    }

    /**
     * Recupera um asset pela chave (com conteudo `value` ou `attachment`).
     *
     * @param  array<string, mixed>  $params  fields
     */
    public function get(int|string $themeId, string $key, array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, "/themes/{$themeId}/assets", array_merge(['asset[key]' => $key], $params));
    }

    /**
     * Cria ou atualiza um asset. Embrulha em `asset` (key + value|attachment|src|source_key).
     *
     * @param  array<string, mixed>  $asset
     */
    public function put(int|string $themeId, array $asset): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/themes/{$themeId}/assets", [], ['asset' => $asset]);
    }

    /**
     * Remove um asset pela chave.
     */
    public function delete(int|string $themeId, string $key): array
    {
        return $this->makeRequest(HttpMethod::DELETE, "/themes/{$themeId}/assets", ['asset[key]' => $key]);
    }
}
