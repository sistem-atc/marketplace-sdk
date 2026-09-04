<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\LojaIntegrada\Endpoints\ProductImage;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\LojaIntegrada\Bases\BaseMethods;

/**
 * Imagens do produto (`/v1/produto_imagem`) e vínculo imagem × variação de grade.
 */
class ProductImageMethods extends BaseMethods
{
    /**
     * Lista imagens de um produto (`?produto={id}`). Paginação `limit/offset`.
     *
     * @param array<string,mixed> $filters
     * @return array<string,mixed>
     */
    public function list(int|string $produtoId, int $limit = 20, int $offset = 0, array $filters = []): array
    {
        return $this->makeRequest(HttpMethod::GET, 'produto_imagem/', array_merge(['produto' => $produtoId, 'limit' => $limit, 'offset' => $offset], $filters));
    }

    /** @return array<string,mixed> */
    public function get(int|string $imageId): array
    {
        return $this->makeRequest(HttpMethod::GET, "produto_imagem/{$imageId}/");
    }

    /**
     * @param array<string,mixed> $data imagem_url, produto (resource_uri), principal, posicao, mime
     * @return array<string,mixed>
     */
    public function create(array $data): array
    {
        return $this->makeRequest(HttpMethod::POST, 'produto_imagem/', [], $data);
    }

    /** @return array<string,mixed> */
    public function delete(int|string $imageId): array
    {
        return $this->makeRequest(HttpMethod::DELETE, "produto_imagem/{$imageId}/");
    }

    /** @return array<string,mixed> */
    public function getVariationImages(int|string $imageId, int|string $gradeVariacaoId): array
    {
        return $this->makeRequest(HttpMethod::GET, "produto_imagem/{$imageId}/grade_variacao/{$gradeVariacaoId}/");
    }

    /**
     * @param array<int,int|string> $imagensIds
     * @return array<string,mixed>
     */
    public function setVariationImages(int|string $imageId, int|string $gradeVariacaoId, array $imagensIds): array
    {
        return $this->makeRequest(HttpMethod::PUT, "produto_imagem/{$imageId}/grade_variacao/{$gradeVariacaoId}/", [], ['imagens_ids' => array_values($imagensIds)]);
    }

    /** @return array<string,mixed> */
    public function deleteVariationImages(int|string $imageId, int|string $gradeVariacaoId): array
    {
        return $this->makeRequest(HttpMethod::DELETE, "produto_imagem/{$imageId}/grade_variacao/{$gradeVariacaoId}/");
    }
}
