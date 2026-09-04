<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\Endpoints\Product;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Magalu\Bases\BaseMethods;
use SistemAtc\Marketplaces\Magalu\DTO\Response\Product\SkuListResponseDTO;
use SistemAtc\Marketplaces\Magalu\DTO\Response\Product\SkuPriceResponseDTO;

/**
 * Endpoints de PORTFÓLIO (catálogo) da Seller API Magalu.
 *
 * Rotas reais do catálogo (confirmadas contra api.magalu.com em 2026-06-26) — o
 * antigo `/seller/v1/items` do ProductMethods responde 404 `resource_not_found`:
 *
 *   - GET /seller/v1/portfolios/skus         → lista paginada de SKUs (envelope
 *     `results`, paginação por `_limit`/`_offset`). NÃO traz preço.
 *   - GET /seller/v1/portfolios/prices/{sku} → preço do SKU (envelope `results`
 *     com `list_price` = DE, `price` = PARA, `normalizer` = divisor; ex.: 5990 /
 *     100 = R$ 59,90).
 *   - GET /seller/v1/portfolios/stocks/{sku} → estoque (exige escopo
 *     `open:portfolio-stocks-seller:read`).
 */
class PortfolioMethods extends BaseMethods
{
    /**
     * Uma página do catálogo de SKUs do seller (envelope `results`).
     *
     */
    public function listSkus(int $limit = 50, int $offset = 0): SkuListResponseDTO
    {
        return SkuListResponseDTO::fromArray($this->makeRequest(HttpMethod::GET, '/seller/v1/portfolios/skus', [
            '_limit' => $limit,
            '_offset' => $offset,
        ]));
    }

    /**
     * Preço de um SKU (envelope `results` com list_price/price/normalizer).
     *
     */
    public function priceBySku(string $sku): SkuPriceResponseDTO
    {
        return SkuPriceResponseDTO::fromArray(
            $this->makeRequest(HttpMethod::GET, '/seller/v1/portfolios/prices/'.rawurlencode($sku)),
        );
    }

    /**
     * Estoque de um SKU (exige escopo open:portfolio-stocks-seller:read).
     *
     * @return array<string, mixed>
     */
    public function stockBySku(string $sku): array
    {
        return $this->makeRequest(HttpMethod::GET, '/seller/v1/portfolios/stocks/'.rawurlencode($sku));
    }


    // ------------------------------------------------------------------
    // SKUs (cadastro/atualizacao) — escrita e' assincrona: responde 202 com
    // `trace_id`; acompanhe em WebhookMethods::listTraces()/getTrace().
    // ------------------------------------------------------------------

    /**
     * Lista crua de SKUs com filtros (GET /seller/v1/portfolios/skus).
     *
     * `status`: PUBLISHED|UNPUBLISHED|BLOCKED|UNDER_REVIEW|DELETING|DELETED;
     * `group_id` (agrupador de variacoes); `_sort`: sku|created_at|updated_at|_id
     * com `:asc|:desc` (default created_at:asc); `_limit` default 10.
     *
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function searchSkus(array $filters = [], int $limit = 10, int $offset = 0, ?string $sort = null): array
    {
        $query = array_merge($filters, ['_limit' => $limit, '_offset' => $offset]);
        if ($sort !== null) $query['_sort'] = $sort;

        return $this->makeRequest(HttpMethod::GET, '/seller/v1/portfolios/skus', $query);
    }

    /**
     * SKU completo (GET /seller/v1/portfolios/skus/{sku}).
     *
     * @return array<string, mixed>
     */
    public function getSku(string $sku): array
    {
        return $this->makeRequest(HttpMethod::GET, '/seller/v1/portfolios/skus/'.rawurlencode($sku));
    }

    /**
     * Cria SKU (POST /seller/v1/portfolios/skus) — 202 + trace_id.
     *
     * Body obrigatorio: `active`, `attributes[]`, `brand`, `channels[] {id}`,
     * `datasheet[]`, `description`, `dimensions[] {name: product|package, ...}`,
     * `extra_data[]`, `group {id, main_variation}`, `identifiers[] {type: ean|isbn, value}`,
     * `images[]`, `sku`, `title`; opcionais `category {id}`, `condition`, `fulfillment`, `has_ean`.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function createSku(array $data): array
    {
        return $this->makeRequest(HttpMethod::POST, '/seller/v1/portfolios/skus', [], $data);
    }

    /**
     * Substitui o SKU inteiro (PUT /seller/v1/portfolios/skus/{sku}) — 202 + trace_id.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function replaceSku(string $sku, array $data): array
    {
        return $this->makeRequest(HttpMethod::PUT, '/seller/v1/portfolios/skus/'.rawurlencode($sku), [], $data);
    }

    /**
     * Atualizacao parcial do SKU (PATCH /seller/v1/portfolios/skus/{sku}) — 202 + trace_id.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function patchSku(string $sku, array $data): array
    {
        return $this->makeRequest(HttpMethod::PATCH, '/seller/v1/portfolios/skus/'.rawurlencode($sku), [], $data);
    }

    /**
     * Motivos de reprovacao/validacao do SKU no canal
     * (GET /seller/v1/portfolios/skus/{sku}/validation-info).
     *
     * @return array<string, mixed>
     */
    public function skuValidationInfo(string $sku): array
    {
        return $this->makeRequest(HttpMethod::GET, '/seller/v1/portfolios/skus/'.rawurlencode($sku).'/validation-info');
    }

    // ------------------------------------------------------------------
    // Precos — valores INTEIROS em centavos com `normalizer` (100).
    // ------------------------------------------------------------------

    /**
     * Cria preco do SKU no canal (POST /seller/v1/portfolios/prices/{sku}) — 202 + trace_id.
     * `listPrice` = DE, `price` = PARA, ambos em centavos.
     *
     * @param array<string, mixed> $metadata
     * @return array<string, mixed>
     */
    public function createPrice(
        string $sku,
        string $channelId,
        int $listPrice,
        int $price,
        string $currency = 'BRL',
        int $normalizer = 100,
        array $metadata = [],
    ): array {
        $body = [
            'channel' => ['id' => $channelId],
            'currency' => $currency,
            'list_price' => $listPrice,
            'price' => $price,
            'normalizer' => $normalizer,
        ];
        if ($metadata) $body['metadata'] = $metadata;

        return $this->makeRequest(HttpMethod::POST, '/seller/v1/portfolios/prices/'.rawurlencode($sku), [], $body);
    }

    /**
     * Atualiza preco parcialmente (PATCH /seller/v1/portfolios/prices/{sku}) — 202 + trace_id.
     * Body livre: `channel.id`, `currency`, `list_price`, `price`, `normalizer`, `metadata`.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function patchPrice(string $sku, array $data): array
    {
        return $this->makeRequest(HttpMethod::PATCH, '/seller/v1/portfolios/prices/'.rawurlencode($sku), [], $data);
    }

    // ------------------------------------------------------------------
    // Estoque — escopo open:portfolio-stocks-seller:write.
    // ------------------------------------------------------------------

    /**
     * Cria posicao de estoque (POST /seller/v1/portfolios/stocks/{sku}) — 202 + trace_id.
     * `branchId` = filial/armazem (opcional); `type` so' aceita AVAILABLE.
     *
     * @param array<string, mixed> $metadata
     * @return array<string, mixed>
     */
    public function createStock(
        string $sku,
        string $channelId,
        int $quantity,
        ?string $branchId = null,
        array $metadata = [],
    ): array {
        $body = ['channel' => ['id' => $channelId], 'quantity' => $quantity, 'type' => 'AVAILABLE'];
        if ($branchId !== null) $body['branch'] = ['id' => $branchId];
        if ($metadata) $body['metadata'] = $metadata;

        return $this->makeRequest(HttpMethod::POST, '/seller/v1/portfolios/stocks/'.rawurlencode($sku), [], $body);
    }

    /**
     * Atualiza estoque (PATCH /seller/v1/portfolios/stocks/{sku}) — 202 + trace_id.
     *
     * @param array<string, mixed> $metadata
     * @return array<string, mixed>
     */
    public function patchStock(
        string $sku,
        string $channelId,
        int $quantity,
        ?string $branchId = null,
        array $metadata = [],
    ): array {
        $body = ['channel' => ['id' => $channelId], 'quantity' => $quantity, 'type' => 'AVAILABLE'];
        if ($branchId !== null) $body['branch'] = ['id' => $branchId];
        if ($metadata) $body['metadata'] = $metadata;

        return $this->makeRequest(HttpMethod::PATCH, '/seller/v1/portfolios/stocks/'.rawurlencode($sku), [], $body);
    }

    // ------------------------------------------------------------------
    // Videos do produto — fluxo: criar (recebe upload_url) → PUT do arquivo na
    // upload_url (fora da API) → confirmUpload() ou markVideoUploaded().
    // ------------------------------------------------------------------

    /**
     * Todos os videos do seller (GET /seller/v1/portfolios/medias/videos).
     * Filtros: `title`, `status`, `sku`, `created_at_gte|lte`; `_limit` default 10.
     *
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function listVideos(array $filters = [], int $limit = 10, int $offset = 0): array
    {
        return $this->makeRequest(HttpMethod::GET, '/seller/v1/portfolios/medias/videos', array_merge($filters, [
            '_limit' => $limit,
            '_offset' => $offset,
        ]));
    }

    /**
     * Videos de um SKU (GET /seller/v1/portfolios/medias/{sku}/videos).
     *
     * @param array<string, mixed> $filters
     * @return array<string, mixed>
     */
    public function listVideosBySku(string $sku, array $filters = [], int $limit = 10, int $offset = 0): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            '/seller/v1/portfolios/medias/'.rawurlencode($sku).'/videos',
            array_merge($filters, ['_limit' => $limit, '_offset' => $offset]),
        );
    }

    /**
     * Um video do SKU (GET /seller/v1/portfolios/medias/{sku}/videos/{id}).
     *
     * @return array<string, mixed>
     */
    public function getVideo(string $sku, string $videoId): array
    {
        return $this->makeRequest(HttpMethod::GET, '/seller/v1/portfolios/medias/'.rawurlencode($sku)."/videos/{$videoId}");
    }

    /**
     * Cria video para um SKU (POST /seller/v1/portfolios/medias/{sku}/videos).
     * Retorna `upload_url` + `video_uuid`. `fileType`: MP4|HLS.
     *
     * @return array<string, mixed>
     */
    public function createVideo(string $sku, string $title, string $fileType = 'MP4'): array
    {
        return $this->makeRequest(HttpMethod::POST, '/seller/v1/portfolios/medias/'.rawurlencode($sku).'/videos', [], [
            'title' => $title,
            'file_type' => $fileType,
        ]);
    }

    /**
     * Cria o mesmo video para varios SKUs (POST /seller/v1/portfolios/medias/videos).
     * Devolve uma lista (um upload_url por SKU).
     *
     * @param list<string> $skus
     * @return array<string, mixed>
     */
    public function createVideoBatch(array $skus, string $title, string $fileType = 'MP4'): array
    {
        return $this->makeRequest(HttpMethod::POST, '/seller/v1/portfolios/medias/videos', [], [
            'title' => $title,
            'file_type' => $fileType,
            'sku' => array_values($skus),
        ]);
    }

    /**
     * Marca o upload como concluido no contexto do SKU
     * (PATCH /seller/v1/portfolios/medias/{sku}/videos/{id}, status=upload_complete).
     *
     * @return array<string, mixed>
     */
    public function markVideoUploaded(string $sku, string $videoId): array
    {
        return $this->makeRequest(
            HttpMethod::PATCH,
            '/seller/v1/portfolios/medias/'.rawurlencode($sku)."/videos/{$videoId}",
            [],
            ['status' => 'upload_complete'],
        );
    }

    /**
     * Confirma o upload pelo id do video (POST /seller/v1/portfolios/videos/{id}/confirm-upload).
     *
     * @return array<string, mixed>
     */
    public function confirmVideoUpload(string $videoId): array
    {
        return $this->makeRequest(HttpMethod::POST, "/seller/v1/portfolios/videos/{$videoId}/confirm-upload");
    }
}
