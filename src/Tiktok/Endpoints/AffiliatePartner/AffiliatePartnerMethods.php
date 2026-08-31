<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\Endpoints\AffiliatePartner;

use InvalidArgumentException;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Tiktok\Bases\BaseMethods;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliatePartner\CampaignCreatorPerformanceResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliatePartner\CampaignDetailResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliatePartner\CampaignListResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliatePartner\CampaignProductListResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliatePartner\CampaignProductPerformanceResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliatePartner\CapAffiliateOrderSearchResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliatePartner\CreatedCampaignResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliatePartner\CreatorContentStatisticsResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliatePartner\CreatorSampleStatusResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliatePartner\MultiProductPromotionLinkResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliatePartner\ProductPromotionLinkResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliatePartner\TapAffiliateOrderSearchResponseDTO;

/**
 * TikTok Shop Affiliate Partner API — o lado da AGENCIA/parceiro de afiliados
 * (TAP/CAP), nao o do vendedor.
 *
 * Quem chama aqui e' o parceiro que monta campanha, recruta vendedor, aprova
 * produto e distribui link pros criadores. Duas coisas valem pra familia toda
 * e sao a origem de quase todo erro de apuracao:
 *
 * 1. `category_asset_cipher` — identificador do parceiro, obrigatorio em quase
 *    todos os endpoints e passado na QUERY (nao e' o `shop_cipher`, que o
 *    BaseMethods ja' injeta sozinho). Vem do Get Authorized Category Assets.
 * 2. TAXA e' INT em CENTESIMO DE PONTO PERCENTUAL: 1000 = 10,00%. Dividir por
 *    100 achando que e' percentual erra por 100x.
 *
 * Versoes divergem por endpoint (202405 pro nucleo de campanha, 202501/202508
 * pra performance, 202505 pro lote de links, 202603 pros pedidos).
 */
class AffiliatePartnerMethods extends BaseMethods
{
    /** Nucleo de campanha (criar/editar/publicar/detalhe/lista/produtos). */
    private const DEFAULT_VERSION = '202405';

    /** Performance por PRODUTO da campanha. */
    private const PRODUCT_PERFORMANCE_VERSION = '202501';

    /** Lote de links de promocao. */
    private const BATCH_LINK_VERSION = '202505';

    /** Performance por CRIADOR + estatisticas de conteudo + status de amostra. */
    private const CREATOR_PERFORMANCE_VERSION = '202508';

    /** Busca de pedidos de afiliado (TAP e CAP). */
    private const ORDER_SEARCH_VERSION = '202603';

    /** Teto de produtos por chamada de gerar link em lote. */
    private const MAX_BATCH_LINK_PRODUCTS = 50;

    /** Piso e teto da comissao aceitos pelo TikTok, em centesimos de %. */
    private const MIN_COMMISSION_RATE = 100;

    private const MAX_COMMISSION_RATE = 8000;

    /**
     * Cria a campanha (nasce em rascunho — so' vale depois de publishCampaign).
     *
     * @param  int  $commissionRate  centesimos de %: 1000 = 10,00%. Faixa aceita: 100 a 8000
     * @param  array<string, string>  $contactInfo  whatsapp/email/phone/zalo/viber/line — qual e' obrigatorio depende do mercado alvo
     * @param  list<string>  $targetShopCodes  shop codes do Seller Center que podem se inscrever
     * @param  list<string>  $targetSellerTypes  alternativa ampla ao codigo loja a loja (ex.: LOCAL)
     */
    public function createCampaign(
        string $categoryAssetCipher,
        string $name,
        string $description,
        int $campaignStartTime,
        int $campaignEndTime,
        int $registrationStartTime,
        int $registrationEndTime,
        int $commissionRate,
        array $contactInfo,
        array $targetShopCodes = [],
        array $targetSellerTypes = [],
        string $version = self::DEFAULT_VERSION,
    ): CreatedCampaignResponseDTO {
        $this->assertCommissionRate($commissionRate);

        $body = [
            'name' => $name,
            'description' => $description,
            'campaign_start_time' => $campaignStartTime,
            'campaign_end_time' => $campaignEndTime,
            'registration_start_time' => $registrationStartTime,
            'registration_end_time' => $registrationEndTime,
            'commission_rate' => $commissionRate,
            'contact_info' => $contactInfo,
        ];
        if ($targetShopCodes !== []) {
            $body['target_shop_codes'] = $targetShopCodes;
        }
        if ($targetSellerTypes !== []) {
            $body['target_seller_types'] = $targetSellerTypes;
        }

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/affiliate_partner/{$version}/campaigns",
            ['category_asset_cipher' => $categoryAssetCipher],
            $body,
        );

        return CreatedCampaignResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Edicao PARCIAL da campanha — manda so' o que mudou.
     *
     * O que da' pra editar depende do status: datas de inicio so' enquanto
     * READY/UPCOMING, e nada de datas quando ja' esta' CLOSED. Resposta e'
     * `data: {}`.
     *
     * @param  array<string, mixed>  $fields  subconjunto dos campos de createCampaign, em snake_case
     * @return array<string, mixed>
     */
    public function editCampaign(
        string $categoryAssetCipher,
        string $campaignId,
        array $fields,
        string $version = self::DEFAULT_VERSION,
    ): array {
        if (isset($fields['commission_rate'])) {
            $this->assertCommissionRate((int) $fields['commission_rate']);
        }

        return $this->makeRequest(
            HttpMethod::POST,
            "/affiliate_partner/{$version}/campaigns/{$campaignId}/partial_edit",
            ['category_asset_cipher' => $categoryAssetCipher],
            $fields,
        );
    }

    /**
     * Publica a campanha (e' o que a torna visivel pros vendedores).
     *
     * Resposta e' `data: {}`.
     *
     * @return array<string, mixed>
     */
    public function publishCampaign(
        string $categoryAssetCipher,
        string $campaignId,
        string $version = self::DEFAULT_VERSION,
    ): array {
        return $this->makeRequest(
            HttpMethod::POST,
            "/affiliate_partner/{$version}/campaigns/{$campaignId}/publish",
            ['category_asset_cipher' => $categoryAssetCipher],
            [],
        );
    }

    /** Detalhe de uma campanha (traz contato, lojas alvo e comissao). */
    public function getCampaignDetail(
        string $categoryAssetCipher,
        string $campaignId,
        string $version = self::DEFAULT_VERSION,
    ): CampaignDetailResponseDTO {
        $response = $this->makeRequest(
            HttpMethod::GET,
            "/affiliate_partner/{$version}/campaigns/{$campaignId}",
            ['category_asset_cipher' => $categoryAssetCipher],
        );

        return CampaignDetailResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Lista campanhas. A listagem e' MAGRA (sem comissao/contato/lojas alvo) —
     * pra isso vai o getCampaignDetail().
     *
     * @param  string|null  $status  READY | UPCOMING | ONGOING | CLOSED | UNSPECIFIED
     * @param  string  $type  MY_CAMPAIGNS (default) | GS_SELLING_CAMPAIGNS | SELLER_CAMPAIGNS | EXCLUSIVE_TIKTOK_SHOP
     * @param  string|null  $queryTypeFilter  filtro extra, so' vale com type SELLER_CAMPAIGNS ou EXCLUSIVE_TIKTOK_SHOP
     */
    public function getCampaigns(
        string $categoryAssetCipher,
        int $pageSize = 10,
        string $pageToken = '',
        ?string $status = null,
        string $type = 'MY_CAMPAIGNS',
        ?string $queryTypeFilter = null,
        string $version = self::DEFAULT_VERSION,
    ): CampaignListResponseDTO {
        $query = [
            'category_asset_cipher' => $categoryAssetCipher,
            'page_size' => $pageSize,
            'type' => $type,
        ];
        if ($pageToken !== '') {
            $query['page_token'] = $pageToken;
        }
        if ($status !== null) {
            $query['status'] = $status;
        }
        if ($queryTypeFilter !== null) {
            $query['query_type_filter'] = $queryTypeFilter;
        }

        $response = $this->makeRequest(HttpMethod::GET, "/affiliate_partner/{$version}/campaigns", $query);

        return CampaignListResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Produtos inscritos na campanha, com SKUs, estoque e as tres comissoes.
     *
     * @param  string|null  $reviewStatus  PENDING | APPROVED | REJECTED | PENDING_CLOSED | CLOSED
     * @param  array<string, string>  $filters  product_name | product_id | shop_name | category_id
     */
    public function getCampaignProducts(
        string $categoryAssetCipher,
        string $campaignId,
        int $pageSize = 20,
        string $pageToken = '',
        ?string $reviewStatus = null,
        array $filters = [],
        string $version = self::DEFAULT_VERSION,
    ): CampaignProductListResponseDTO {
        $query = ['category_asset_cipher' => $categoryAssetCipher, 'page_size' => $pageSize] + $filters;
        if ($pageToken !== '') {
            $query['page_token'] = $pageToken;
        }
        if ($reviewStatus !== null) {
            $query['review_status'] = $reviewStatus;
        }

        $response = $this->makeRequest(
            HttpMethod::GET,
            "/affiliate_partner/{$version}/campaigns/{$campaignId}/products",
            $query,
        );

        return CampaignProductListResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Aprova/recusa um produto que o vendedor inscreveu na campanha.
     *
     * `REJECT_FOREVER` bloqueia o produto pra sempre nesta campanha — nao ha'
     * desfazer pela API. Resposta e' `data: {}`.
     *
     * @param  string  $reviewResult  APPROVE | REJECT | REJECT_FOREVER
     * @param  list<string>  $rejectReasons  obrigatorio quando recusa
     * @return array<string, mixed>
     */
    public function reviewCampaignProduct(
        string $categoryAssetCipher,
        string $campaignId,
        string $productId,
        string $reviewResult,
        array $rejectReasons = [],
        string $version = self::DEFAULT_VERSION,
    ): array {
        $body = ['review_result' => $reviewResult];
        if ($rejectReasons !== []) {
            $body['reject_reasons'] = $rejectReasons;
        }

        return $this->makeRequest(
            HttpMethod::POST,
            "/affiliate_partner/{$version}/campaigns/{$campaignId}/products/{$productId}/review",
            ['category_asset_cipher' => $categoryAssetCipher],
            $body,
        );
    }

    /**
     * Gera o link de promocao de UM produto pros criadores.
     *
     * `$creatorCommissionRate` (centesimos de %) e' a fatia que o criador
     * leva e tem que ser <= a comissao total que o vendedor bancou — o TikTok
     * recusa acima disso.
     */
    public function generateProductPromotionLink(
        string $categoryAssetCipher,
        string $campaignId,
        string $productId,
        int $creatorCommissionRate,
        string $version = self::DEFAULT_VERSION,
    ): ProductPromotionLinkResponseDTO {
        $response = $this->makeRequest(
            HttpMethod::POST,
            "/affiliate_partner/{$version}/campaigns/{$campaignId}/products/{$productId}/promotion_link/generate",
            ['category_asset_cipher' => $categoryAssetCipher],
            ['creator_commission_rate' => $creatorCommissionRate],
        );

        return ProductPromotionLinkResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Gera links em LOTE (max 50 produtos).
     *
     * Ao contrario da versao unitaria, este endpoint NAO recebe comissao — usa
     * a que ja' esta' na campanha. E o retorno e' parcial: confira
     * `failedProductIds` antes de considerar o lote gerado.
     *
     * @param  list<string>  $productIds
     */
    public function generateProductPromotionLinks(
        string $campaignId,
        array $productIds,
        ?string $categoryAssetCipher = null,
        string $version = self::BATCH_LINK_VERSION,
    ): MultiProductPromotionLinkResponseDTO {
        if (count($productIds) > self::MAX_BATCH_LINK_PRODUCTS) {
            throw new InvalidArgumentException(sprintf(
                'Max %d produtos por lote (recebido %d).',
                self::MAX_BATCH_LINK_PRODUCTS,
                count($productIds),
            ));
        }

        $query = [];
        if ($categoryAssetCipher !== null) {
            $query['category_asset_cipher'] = $categoryAssetCipher;
        }

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/affiliate_partner/{$version}/campaigns/{$campaignId}/products/promotion_links/generate_batch",
            $query,
            ['product_ids' => $productIds],
        );

        return MultiProductPromotionLinkResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Criadores promovendo UM produto da campanha (com status de amostra,
     * contagem de video/live e comissao vigente).
     *
     * Guarde o `affiliateProductId` de cada criador: e' a chave exigida pelo
     * getCreatorContentStatistics().
     */
    public function getCampaignProductCreators(
        string $categoryAssetCipher,
        string $campaignId,
        string $productId,
        int $pageSize = 50,
        string $pageToken = '',
        string $version = self::CREATOR_PERFORMANCE_VERSION,
    ): CampaignCreatorPerformanceResponseDTO {
        $query = ['category_asset_cipher' => $categoryAssetCipher, 'page_size' => $pageSize];
        if ($pageToken !== '') {
            $query['page_token'] = $pageToken;
        }

        $response = $this->makeRequest(
            HttpMethod::GET,
            "/affiliate_partner/{$version}/campaigns/{$campaignId}/products/{$productId}/performance",
            $query,
        );

        return CampaignCreatorPerformanceResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Performance de TODOS os produtos da campanha (KPIs estimado × real).
     *
     * Unico endpoint da familia que NAO pede `category_asset_cipher`.
     */
    public function getCampaignProductsPerformance(
        string $campaignId,
        int $pageSize = 50,
        string $pageToken = '',
        string $version = self::PRODUCT_PERFORMANCE_VERSION,
    ): CampaignProductPerformanceResponseDTO {
        $query = ['page_size' => $pageSize];
        if ($pageToken !== '') {
            $query['page_token'] = $pageToken;
        }

        $response = $this->makeRequest(
            HttpMethod::GET,
            "/affiliate_partner/{$version}/campaigns/{$campaignId}/products/performance",
            $query,
        );

        return CampaignProductPerformanceResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Estatisticas dos conteudos (videos e lives) de um criador pro produto.
     *
     * @param  string  $creatorTempId  id TEMPORARIO do criador, vindo da rota de performance — nao e' o creator_open_id
     * @param  string  $affiliateProductId  vem de `promotionCreators[].affiliateProductId`
     * @param  string|null  $contentType  '1' = VIDEO, '2' = LIVE_ROOM (o filtro usa o codigo; a resposta devolve o nome)
     */
    public function getCreatorContentStatistics(
        string $categoryAssetCipher,
        string $campaignId,
        string $productId,
        string $creatorTempId,
        string $affiliateProductId,
        ?string $contentType = null,
        string $version = self::CREATOR_PERFORMANCE_VERSION,
    ): CreatorContentStatisticsResponseDTO {
        $query = [
            'category_asset_cipher' => $categoryAssetCipher,
            'affiliate_product_id' => $affiliateProductId,
        ];
        if ($contentType !== null) {
            $query['content_type'] = $contentType;
        }

        $response = $this->makeRequest(
            HttpMethod::GET,
            "/affiliate_partner/{$version}/campaigns/{$campaignId}/products/{$productId}/creator/{$creatorTempId}/content/statistics",
            $query,
        );

        return CreatorContentStatisticsResponseDTO::fromArray($response['data'] ?? []);
    }

    /** Rastreio da amostra que o vendedor mandou pro criador. */
    public function getCreatorSampleStatus(
        string $categoryAssetCipher,
        string $campaignId,
        string $productId,
        string $creatorTempId,
        string $version = self::CREATOR_PERFORMANCE_VERSION,
    ): CreatorSampleStatusResponseDTO {
        $response = $this->makeRequest(
            HttpMethod::GET,
            "/affiliate_partner/{$version}/campaigns/{$campaignId}/products/{$productId}/creator/{$creatorTempId}/content/statistics/sample/status",
            ['category_asset_cipher' => $categoryAssetCipher],
        );

        return CreatorSampleStatusResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Busca pedidos do programa CAP (comissao rateada com a AGENCIA).
     *
     * Janela em epoch de SEGUNDOS e limitada a 3 MESES pelo TikTok. Cada linha
     * e' um SKU, nao um pedido.
     *
     * @param  array<string, mixed>  $filters  order_id | product_id | settle_status | create_time_ge | create_time_lt
     */
    public function searchCapOrders(
        string $categoryAssetCipher,
        array $filters = [],
        int $pageSize = 20,
        string $pageToken = '',
        string $version = self::ORDER_SEARCH_VERSION,
    ): CapAffiliateOrderSearchResponseDTO {
        $query = ['category_asset_cipher' => $categoryAssetCipher, 'page_size' => $pageSize];
        if ($pageToken !== '') {
            $query['page_token'] = $pageToken;
        }

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/affiliate_partner/{$version}/cap_order/search",
            $query,
            $filters,
        );

        return CapAffiliateOrderSearchResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Busca pedidos do programa TAP (comissao rateada com o PARCEIRO).
     *
     * Mesma janela de 3 meses do CAP; os campos de comissao sao outros — ver
     * TapAffiliateSkuOrder.
     *
     * @param  array<string, mixed>  $filters  create_time_ge | create_time_lt | campaign_id
     */
    public function searchTapOrders(
        string $categoryAssetCipher,
        array $filters = [],
        int $pageSize = 20,
        string $pageToken = '',
        string $version = self::ORDER_SEARCH_VERSION,
    ): TapAffiliateOrderSearchResponseDTO {
        $query = ['category_asset_cipher' => $categoryAssetCipher, 'page_size' => $pageSize];
        if ($pageToken !== '') {
            $query['page_token'] = $pageToken;
        }

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/affiliate_partner/{$version}/orders/search",
            $query,
            $filters,
        );

        return TapAffiliateOrderSearchResponseDTO::fromArray($response['data'] ?? []);
    }

    /** O TikTok recusa fora de 100..8000 (1,00% a 80,00%) com erro generico. */
    private function assertCommissionRate(int $rate): void
    {
        if ($rate < self::MIN_COMMISSION_RATE || $rate > self::MAX_COMMISSION_RATE) {
            throw new InvalidArgumentException(sprintf(
                'commission_rate fora da faixa TikTok (%d..%d, centesimos de %%): %d.',
                self::MIN_COMMISSION_RATE,
                self::MAX_COMMISSION_RATE,
                $rate,
            ));
        }
    }
}
