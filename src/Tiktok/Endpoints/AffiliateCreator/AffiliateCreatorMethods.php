<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\Endpoints\AffiliateCreator;

use InvalidArgumentException;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Tiktok\Bases\BaseMethods;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator\AddShowcaseProductsResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator\AffiliateOrderSearchResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator\AffiliateTraceOrderSearchResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator\CreatorProfileResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator\CreatorSampleApplicationDetailResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator\CreatorSampleLabelResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator\MusicSearchResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator\OpenCollaborationProductListResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator\OpenCollaborationProductSearchResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator\PhotoFileUploadResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator\SampleApplicationFulfillmentSearchResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator\SampleApplicationSearchResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator\SharingLinkBatchResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator\ShopProductSearchResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator\ShoppablePhotoPostResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator\ShoppableVideoPostResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator\ShoppableVideoPrecheckResultResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator\ShoppableVideoStatusResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator\ShowcaseOperationResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator\ShowcaseProductSearchResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator\TargetCollaborationSearchResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator\TokoProductMapperResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator\VideoFileUploadResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator\VideoPrecheckTaskResponseDTO;
use SistemAtc\Marketplaces\Tiktok\Support\HttpClientFactory;

/**
 * Affiliate Creator API (`/affiliate_creator/{version}/...`) — o lado CREATOR
 * do programa de afiliados: vitrine, colaboracoes, comissoes, videos/fotos
 * compraveis e, principalmente, AMOSTRAS.
 *
 * ⚠️ TOKEN DIFERENTE DO RESTO DO SDK. Estes endpoints exigem um access token de
 * CREATOR (`user_type = 1`), nao o token de loja usado em `orders()`/
 * `products()`. E o token de creator NAO carrega `shop_cipher`: a integration
 * passada aqui deve vir sem esse setting, senao a assinatura leva um cipher que
 * nao corresponde ao dono do token.
 *
 * ⚠️ SINAL DE AMOSTRA (motivo pratico deste modulo no ERP): as amostras que o
 * vendedor manda pro creator viram PEDIDO no TikTok — `sample_application.
 * main_order_id`. Cruzar esse id com o pedido antes de emitir a nota separa
 * brinde de venda. Ver `searchSampleApplications()` e
 * `getSampleApplicationDetail()`.
 *
 * Convencoes do dominio (valem pra TODOS os metodos daqui):
 * - Datas sao epoch em SEGUNDOS.
 * - Dinheiro e' STRING ja' formatada na moeda local ("Rp9.900").
 * - Taxa de comissao e' INT em centesimos de por cento (3000 = 30%).
 * - Paginacao por cursor (`page_token`/`next_page_token`), exceto Search Music,
 *   que ainda exige repetir o `search_id` em cada pagina.
 */
class AffiliateCreatorMethods extends BaseMethods
{
    /** Teto de produtos por chamada de `addShowcaseProducts`. */
    private const MAX_SHOWCASE_ADD = 20;

    /** Teto de produtos por chamada de `removeShowcaseProducts`. */
    private const MAX_SHOWCASE_REMOVE = 200;

    /** Teto de materiais por lote de geracao de link. */
    private const MAX_SHARING_LINK_MATERIALS = 50;

    // ---------------------------------------------------------------- AMOSTRAS

    /**
     * Lista os pedidos de AMOSTRA do creator.
     *
     * Cada item traz `mainOrderId`: o pedido que a amostra vira depois de
     * aprovada. E' o cruzamento que identifica pedido-de-amostra ANTES da nota.
     *
     * @param  array<int, string>|null  $applicationStatuses  PENDING | AWAITING_SHIPMENT | SHIPPED |
     *                                                        CONTENT_PENDING | COMPLETED | *_CANCELLED ...
     */
    public function searchSampleApplications(
        ?array $applicationStatuses = null,
        int $pageSize = 20,
        string $pageToken = '',
        string $version = '202412',
    ): SampleApplicationSearchResponseDTO {
        $query = ['page_size' => min($pageSize, 50)];
        if ($pageToken !== '') {
            $query['page_token'] = $pageToken;
        }

        $body = $applicationStatuses === null ? [] : ['application_statuses' => $applicationStatuses];

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/affiliate_creator/{$version}/sample_applications/search",
            $query,
            $body,
        );

        return SampleApplicationSearchResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Detalhe de UMA solicitacao de amostra.
     *
     * O par obrigatorio muda com o tipo: `FREE_SAMPLE` exige `applicationId`;
     * `SAMPLE_COUPON`, `SAMPLE_CAMPAIGN` e `REFUNDABLE_SAMPLE` exigem
     * `mainOrderId`. Mandar so' o `productId` volta erro.
     *
     * @param  string  $applicationType  FREE_SAMPLE | SAMPLE_COUPON | SAMPLE_CAMPAIGN | REFUNDABLE_SAMPLE
     */
    public function getSampleApplicationDetail(
        string $productId,
        string $applicationType,
        ?string $applicationId = null,
        ?string $mainOrderId = null,
        string $version = '202412',
    ): CreatorSampleApplicationDetailResponseDTO {
        if ($applicationType === 'FREE_SAMPLE' && $applicationId === null) {
            throw new InvalidArgumentException('application_id e obrigatorio quando application_type = FREE_SAMPLE.');
        }

        if ($applicationType !== 'FREE_SAMPLE' && $mainOrderId === null) {
            throw new InvalidArgumentException(
                "main_order_id e obrigatorio quando application_type = {$applicationType}.",
            );
        }

        $body = array_filter([
            'product_id' => $productId,
            'application_id' => $applicationId,
            'application_type' => $applicationType,
            'main_order_id' => $mainOrderId,
        ], fn ($v) => $v !== null);

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/affiliate_creator/{$version}/sample_applications/single_query",
            [],
            $body,
        );

        return CreatorSampleApplicationDetailResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Contrapartidas de conteudo das amostras (o que o creator ainda deve).
     *
     * Achatado: traz `shopId`/`productId`/`sampleApplicationType` sem precisar
     * abrir cada application.
     *
     * @param  array<int, string>  $fulfillmentStatuses  PENDING | ONGOING | SUCCEED | FAILED |
     *                                                   OVERDUE | SUSPEND | CANCELLED | EXEMPTED
     * @param  string|null  $sortField  expired_time (default) | create_time
     */
    public function searchSampleApplicationFulfillments(
        array $fulfillmentStatuses,
        ?string $sortField = null,
        ?string $sortOrder = null,
        string $version = '202409',
    ): SampleApplicationFulfillmentSearchResponseDTO {
        if ($fulfillmentStatuses === []) {
            throw new InvalidArgumentException('fulfillment_statuses e obrigatorio e nao pode ser vazio.');
        }

        $query = array_filter([
            'sort_field' => $sortField,
            'sort_order' => $sortOrder,
        ], fn ($v) => $v !== null);

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/affiliate_creator/{$version}/sample_applications/fulfillments/search",
            $query,
            ['fulfillment_statuses' => $fulfillmentStatuses],
        );

        return SampleApplicationFulfillmentSearchResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Elegibilidade de AMOSTRA de um produto pro creator do token.
     *
     * Unico endpoint do grupo que responde ANTES de existir pedido: diz se o
     * produto esta' no fluxo de amostra (`canApply`, `status`) e quais SKUs
     * podem ser pedidos.
     */
    public function getApplicableSampleLabel(
        string $productId,
        string $version = '202412',
    ): CreatorSampleLabelResponseDTO {
        $response = $this->makeRequest(
            HttpMethod::GET,
            "/affiliate_creator/{$version}/samples/labels",
            ['product_id' => $productId],
        );

        return CreatorSampleLabelResponseDTO::fromArray($response['data'] ?? []);
    }

    // ------------------------------------------------------- PERFIL / PEDIDOS

    /**
     * Perfil do creator dono do token. Nao aceita parametro — e' sempre "eu".
     */
    public function getCreatorProfile(string $version = '202508'): CreatorProfileResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, "/affiliate_creator/{$version}/profiles");

        return CreatorProfileResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Pedidos de afiliado do creator (comissoes estimadas x realizadas).
     *
     * Se so' um lado da janela for informado, o TikTok completa o outro:
     * `createTimeLt` vira "agora", `createTimeGe` vira a data mais antiga da
     * loja — o que pode varrer a base inteira. Passe os dois.
     */
    public function searchAffiliateOrders(
        ?int $createTimeGe = null,
        ?int $createTimeLt = null,
        int $pageSize = 20,
        string $pageToken = '',
        string $version = '202410',
    ): AffiliateOrderSearchResponseDTO {
        $query = ['page_size' => min($pageSize, 100)];
        if ($pageToken !== '') {
            $query['page_token'] = $pageToken;
        }

        $body = array_filter([
            'create_time_ge' => $createTimeGe,
            'create_time_lt' => $createTimeLt,
        ], fn ($v) => $v !== null);

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/affiliate_creator/{$version}/orders/search",
            $query,
            $body,
        );

        return AffiliateOrderSearchResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Pedidos atribuidos aos sharing links (rastreio por link/publisher).
     *
     * Diferente do `searchAffiliateOrders`, aqui a janela e' obrigatoria e o
     * campo de tempo e' escolhido em `timeType`.
     *
     * @param  string|null  $timeType  CREATE_TIME (default) | PAY_TIME | DELIVERY_TIME | SETTLE_TIME
     */
    public function searchAffiliateTraceOrders(
        int $timeGe,
        int $timeLt,
        ?string $timeType = null,
        int $pageSize = 20,
        string $pageToken = '',
        string $version = '202505',
    ): AffiliateTraceOrderSearchResponseDTO {
        $query = ['page_size' => min($pageSize, 100)];
        if ($pageToken !== '') {
            $query['page_token'] = $pageToken;
        }

        $body = array_filter([
            'time_ge' => $timeGe,
            'time_lt' => $timeLt,
            'time_type' => $timeType,
        ], fn ($v) => $v !== null);

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/affiliate_creator/{$version}/orders/trace/search",
            $query,
            $body,
        );

        return AffiliateTraceOrderSearchResponseDTO::fromArray($response['data'] ?? []);
    }

    // -------------------------------------------------------- SHARING LINKS

    /**
     * Gera links de afiliado GENERICOS (sem publisher nominal), em lote.
     *
     * @param  array<int, string>  $materialIds  max 50
     * @param  string  $materialType  hoje so' `PRODUCT`
     * @param  string|null  $linkType  `TOKO` devolve a URL Tokopedia; vazio devolve a do TikTok Shop
     */
    public function generateGeneralSharingLinks(
        array $materialIds,
        string $materialType = 'PRODUCT',
        ?string $promotionCampaignSchema = null,
        ?string $campaignId = null,
        ?string $linkType = null,
        string $version = '202505',
    ): SharingLinkBatchResponseDTO {
        $body = $this->sharingLinkBody($materialIds, $materialType, $promotionCampaignSchema, $campaignId, $linkType);

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/affiliate_creator/{$version}/affiliate_sharing_links/general_publishers/generate_batch",
            [],
            $body,
        );

        return SharingLinkBatchResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Gera links de afiliado nominais de UM publisher (o id vai no PATH).
     *
     * Os pedidos vindos daqui voltam em `searchAffiliateTraceOrders()` com
     * `trace.type = SPECIFIC` e `trace.id = publisher_id`.
     *
     * @param  array<int, string>  $materialIds  max 50
     */
    public function generatePublisherSharingLinks(
        string $publisherId,
        array $materialIds,
        string $materialType = 'PRODUCT',
        ?string $promotionCampaignSchema = null,
        ?string $campaignId = null,
        ?string $linkType = null,
        string $version = '202504',
    ): SharingLinkBatchResponseDTO {
        $body = $this->sharingLinkBody($materialIds, $materialType, $promotionCampaignSchema, $campaignId, $linkType);

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/affiliate_creator/{$version}/affiliate_sharing_links/publisher/{$publisherId}/generate_batch",
            [],
            $body,
        );

        return SharingLinkBatchResponseDTO::fromArray($response['data'] ?? []);
    }

    // ------------------------------------------------------- COLABORACOES

    /**
     * Busca produtos de colaboracao ABERTA (qualquer creator pode promover).
     *
     * @param  array<int, string>|null  $titleKeywords  max 20 termos, combinados com AND
     * @param  array{amount_ge?: string, amount_lt?: string}|null  $salesPriceRange  dinheiro como STRING
     * @param  array{rate_ge?: int, rate_lt?: int}|null  $commissionRateRange  centesimos de por cento (min 1000)
     * @param  string|null  $sortField  commission_rate | product_sales_price | commission | units_sold
     */
    public function searchOpenCollaborationProducts(
        ?array $titleKeywords = null,
        ?array $salesPriceRange = null,
        ?string $categoryId = null,
        ?array $commissionRateRange = null,
        int $pageSize = 20,
        string $pageToken = '',
        ?string $sortField = null,
        ?string $sortOrder = null,
        string $version = '202405',
    ): OpenCollaborationProductSearchResponseDTO {
        $query = array_filter([
            'page_size' => min($pageSize, 20),
            'page_token' => $pageToken !== '' ? $pageToken : null,
            'sort_field' => $sortField,
            'sort_order' => $sortOrder,
        ], fn ($v) => $v !== null);

        $body = array_filter([
            'title_keywords' => $titleKeywords,
            'sales_price_range' => $salesPriceRange,
            'category' => $categoryId !== null ? ['id' => $categoryId] : null,
            'commission_rate_range' => $commissionRateRange,
        ], fn ($v) => $v !== null);

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/affiliate_creator/{$version}/open_collaborations/products/search",
            $query,
            $body,
        );

        return OpenCollaborationProductSearchResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Colaboracoes abertas de produtos ESPECIFICOS.
     *
     * Apesar de POST, os ids vao na QUERY separados por virgula (o corpo vai
     * vazio) — e' o que o curl oficial faz.
     *
     * @param  array<int, string>  $productIds
     */
    public function getOpenCollaborationProductsByIds(
        array $productIds,
        string $version = '202509',
    ): OpenCollaborationProductListResponseDTO {
        $response = $this->makeRequest(
            HttpMethod::POST,
            "/affiliate_creator/{$version}/open_collaborations/products",
            ['product_ids' => implode(',', $productIds)],
            [],
        );

        return OpenCollaborationProductListResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Colaboracoes DIRECIONADAS (convites nominais) de uma loja.
     *
     * `TARGET_COLLABORATIONS_ID` devolve tambem as encerradas (LIVE, EXPIRED,
     * DELETED, ENDED); busca por NOME devolve so' as LIVE.
     *
     * @param  string|null  $keywordType  TARGET_COLLABORATIONS_ID | TARGET_COLLABORATIONS_NAME
     */
    public function searchTargetCollaborations(
        string $shopId,
        ?string $keywordType = null,
        ?string $keyword = null,
        int $pageSize = 20,
        string $pageToken = '',
        string $version = '202405',
    ): TargetCollaborationSearchResponseDTO {
        $query = ['page_size' => min($pageSize, 100)];
        if ($pageToken !== '') {
            $query['page_token'] = $pageToken;
        }

        $body = array_filter([
            'shop_id' => $shopId,
            'keyword_type' => $keywordType,
            'keyword' => $keyword,
        ], fn ($v) => $v !== null);

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/affiliate_creator/{$version}/target_collaborations/search",
            $query,
            $body,
        );

        return TargetCollaborationSearchResponseDTO::fromArray($response['data'] ?? []);
    }

    // ------------------------------------------------------------- VITRINE

    /**
     * Produtos da vitrine do creator.
     *
     * @param  string  $origin  LIVE (pedido veio da live) | SHOWCASE
     */
    public function getShowcaseProducts(
        string $origin = 'SHOWCASE',
        int $pageSize = 20,
        string $pageToken = '',
        string $version = '202405',
    ): ShowcaseProductSearchResponseDTO {
        $query = [
            'page_size' => min($pageSize, 20),
            'origin' => $origin,
        ];
        if ($pageToken !== '') {
            $query['page_token'] = $pageToken;
        }

        $response = $this->makeRequest(
            HttpMethod::GET,
            "/affiliate_creator/{$version}/showcases/products",
            $query,
        );

        return ShowcaseProductSearchResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Adiciona produtos na vitrine, por id OU por link.
     *
     * FALHA PARCIAL: responde 200 com `errors[]` listando o que nao entrou —
     * resposta sem `errors` e' que significa "tudo certo".
     *
     * @param  string  $addType  PRODUCT_ID | PRODUCT_LINK
     * @param  array<int, string>|null  $productIds  max 20, quando addType = PRODUCT_ID
     */
    public function addShowcaseProducts(
        string $addType,
        ?array $productIds = null,
        ?string $productLink = null,
        string $version = '202405',
    ): AddShowcaseProductsResponseDTO {
        if ($productIds !== null && count($productIds) > self::MAX_SHOWCASE_ADD) {
            throw new InvalidArgumentException(sprintf(
                'Max %d produtos por chamada (recebeu %d).',
                self::MAX_SHOWCASE_ADD,
                count($productIds),
            ));
        }

        $body = array_filter([
            'add_type' => $addType,
            'product_ids' => $productIds,
            'product_link' => $productLink,
        ], fn ($v) => $v !== null);

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/affiliate_creator/{$version}/showcases/products/add",
            [],
            $body,
        );

        return AddShowcaseProductsResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Remove produtos da vitrine. Max 200 por chamada.
     *
     * @param  array<int, string>  $productIds
     */
    public function removeShowcaseProducts(
        array $productIds,
        string $version = '202409',
    ): ShowcaseOperationResponseDTO {
        if (count($productIds) > self::MAX_SHOWCASE_REMOVE) {
            throw new InvalidArgumentException(sprintf(
                'Max %d produtos por chamada (recebeu %d).',
                self::MAX_SHOWCASE_REMOVE,
                count($productIds),
            ));
        }

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/affiliate_creator/{$version}/showcases/products",
            [],
            ['product_ids' => $productIds],
        );

        return ShowcaseOperationResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Fixa produtos no topo da vitrine, NA ORDEM em que forem passados.
     *
     * @param  array<int, string>  $productIds
     */
    public function topShowcaseProducts(
        array $productIds,
        string $version = '202409',
    ): ShowcaseOperationResponseDTO {
        $response = $this->makeRequest(
            HttpMethod::POST,
            "/affiliate_creator/{$version}/showcases/products/top",
            [],
            ['product_ids' => $productIds],
        );

        return ShowcaseOperationResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Catalogo da loja visto pelo creator (base pra montar vitrine/video).
     *
     * @param  string|null  $sortField  PRODUCT_ID (default) | PRICE | SALE
     */
    public function getShopProducts(
        ?string $titleKeyword = null,
        ?string $sortField = null,
        ?string $sortOrder = null,
        int $pageSize = 20,
        string $pageToken = '',
        string $version = '202509',
    ): ShopProductSearchResponseDTO {
        $query = array_filter([
            'page_size' => min($pageSize, 100),
            'title_keyword' => $titleKeyword,
            'sort_field' => $sortField,
            'sort_order' => $sortOrder,
            'page_token' => $pageToken !== '' ? $pageToken : null,
        ], fn ($v) => $v !== null);

        $response = $this->makeRequest(
            HttpMethod::GET,
            "/affiliate_creator/{$version}/shop_products",
            $query,
        );

        return ShopProductSearchResponseDTO::fromArray($response['data'] ?? []);
    }

    // ------------------------------------------------ VIDEO / FOTO / MUSICA

    /**
     * Sobe o arquivo de VIDEO e devolve o `file_id` usado na publicacao.
     *
     * A doc lista o metodo como GET, mas o curl oficial e' POST multipart —
     * seguimos o curl. Teto de 10 MB; acima disso e' a API de upload grande.
     */
    public function uploadShoppableVideoFile(
        string $fileContents,
        string $fileName,
        string $version = '202505',
    ): VideoFileUploadResponseDTO {
        $data = $this->uploadMultipart("/affiliate_creator/{$version}/videos/video_files", $fileContents, $fileName);

        return VideoFileUploadResponseDTO::fromArray($data['data'] ?? []);
    }

    /**
     * Sobe o arquivo de FOTO e devolve o `photo_uri` usado na publicacao.
     *
     * Mesma pegadinha do video: doc diz GET, curl oficial e' POST multipart.
     * Teto de 10 MB, proporcao entre 9:16 e 16:9.
     */
    public function uploadShoppablePhotoFile(
        string $fileContents,
        string $fileName,
        string $version = '202511',
    ): PhotoFileUploadResponseDTO {
        $data = $this->uploadMultipart("/affiliate_creator/{$version}/photos/photo_files", $fileContents, $fileName);

        return PhotoFileUploadResponseDTO::fromArray($data['data'] ?? []);
    }

    /**
     * Pre-checa o video ANTES de publicar. Assincrono: devolve so' o task_id.
     *
     * @param  string  $fileId  o `id` devolvido por `uploadShoppableVideoFile()`
     * @param  string  $anchorTitle  titulo do anchor do produto, < 30 caracteres
     */
    public function precheckVideoContent(
        string $fileId,
        string $productId,
        string $anchorTitle,
        string $version = '202511',
    ): VideoPrecheckTaskResponseDTO {
        $response = $this->makeRequest(
            HttpMethod::POST,
            "/affiliate_creator/{$version}/videos/precheck_task",
            [],
            [
                'video_info' => ['file_id' => $fileId],
                'product_link_info' => ['product_id' => $productId, 'title' => $anchorTitle],
            ],
        );

        return VideoPrecheckTaskResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Veredicto do pre-check. Enquanto `status` for `PROCESSING`, reconsulte —
     * nao e' falha.
     */
    public function getShoppableVideoPrecheckResult(
        string $taskId,
        string $version = '202601',
    ): ShoppableVideoPrecheckResultResponseDTO {
        $response = $this->makeRequest(
            HttpMethod::GET,
            "/affiliate_creator/{$version}/videos/precheck_tasks/{$taskId}",
        );

        return ShoppableVideoPrecheckResultResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Publica um video compravel ligado a UM produto.
     *
     * `coverUri` e `coverTimestampMs` sao mutuamente exclusivos — mandando os
     * dois, o TikTok usa o `coverUri`. Sem nenhum, a capa e' o 1o frame.
     *
     * @param  int|null  $coverTimestampMs  milissegundos (unica medida em ms do modulo)
     */
    public function postShoppableVideo(
        string $fileId,
        string $title,
        string $productId,
        string $anchorTitle,
        ?string $coverUri = null,
        ?int $coverTimestampMs = null,
        ?string $musicId = null,
        ?bool $isAiGenerated = null,
        string $version = '202607',
    ): ShoppableVideoPostResponseDTO {
        $videoInfo = array_filter([
            'file_id' => $fileId,
            'title' => $title,
            'cover_uri' => $coverUri,
            'cover_timestamp_ms' => $coverTimestampMs,
            'music_id' => $musicId,
            'is_ai_generated' => $isAiGenerated,
        ], fn ($v) => $v !== null);

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/affiliate_creator/{$version}/videos",
            [],
            [
                'video_info' => $videoInfo,
                'product_link_info' => ['product_id' => $productId, 'title' => $anchorTitle],
            ],
        );

        return ShoppableVideoPostResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Status de publicacao do video: SUCCESS | FAIL | PROCESSING.
     */
    public function getShoppableVideoStatus(
        string $videoId,
        string $version = '202509',
    ): ShoppableVideoStatusResponseDTO {
        $response = $this->makeRequest(
            HttpMethod::GET,
            "/affiliate_creator/{$version}/videos/{$videoId}/status",
        );

        return ShoppableVideoStatusResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Publica um post de FOTOS compravel.
     *
     * Diferente do video, aqui o link pode apontar pra loja inteira
     * (`LINK_TO_SHOP`) e nao so' pra um produto.
     *
     * @param  array<int, string>  $photoFileUris  uris de `uploadShoppablePhotoFile()`
     * @param  array<string, mixed>|null  $linkInfo  {post_type, shop_info?, links?} — ver doc
     */
    public function postShoppablePhotos(
        array $photoFileUris,
        ?array $linkInfo = null,
        ?string $title = null,
        ?string $musicId = null,
        string $version = '202607',
    ): ShoppablePhotoPostResponseDTO {
        $body = array_filter([
            'photos_info' => array_map(fn (string $uri) => ['photo_file_uris' => $uri], $photoFileUris),
            'music_id' => $musicId,
            'title' => $title,
            'link_info' => $linkInfo,
        ], fn ($v) => $v !== null);

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/affiliate_creator/{$version}/photos",
            [],
            $body,
        );

        return ShoppablePhotoPostResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Busca na biblioteca de musica.
     *
     * ARMADILHA DE PAGINACAO: a 1a pagina devolve um `searchId` que precisa ser
     * repetido em TODAS as seguintes; sem ele a paginacao quebra em silencio.
     * E o fim de lista e' `hasMore = false`, nao token vazio.
     */
    public function searchMusic(
        string $keyword,
        ?string $searchId = null,
        string $pageToken = '',
        ?int $pageSize = null,
        ?string $region = null,
        ?string $language = null,
        string $version = '202602',
    ): MusicSearchResponseDTO {
        $query = array_filter([
            'keyword' => $keyword,
            'search_id' => $searchId,
            'page_token' => $pageToken !== '' ? $pageToken : null,
            // page_size e' declarado STRING na doc deste endpoint (so' aqui).
            'page_size' => $pageSize !== null ? (string) $pageSize : null,
            'region' => $region,
            'language' => $language,
        ], fn ($v) => $v !== null);

        $response = $this->makeRequest(
            HttpMethod::GET,
            "/affiliate_creator/{$version}/music/search",
            $query,
        );

        return MusicSearchResponseDTO::fromArray($response['data'] ?? []);
    }

    // --------------------------------------------------------------- TOKOPEDIA

    /**
     * De/para de id de produto Tokopedia -> TikTok Shop (versao GET).
     *
     * A doc lista `toko_pids` como corpo, mas o curl oficial do endpoint GET
     * nao manda corpo nenhum — e um GET nao carrega body pelo cliente HTTP.
     * Pra filtrar por ids use `mapTokoProduct()` (V2, POST).
     */
    public function getTokoProductMappers(string $version = '202606'): TokoProductMapperResponseDTO
    {
        $response = $this->makeRequest(
            HttpMethod::GET,
            "/affiliate_creator/{$version}/toko_product_mappers",
        );

        return TokoProductMapperResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * De/para de id Tokopedia -> TikTok Shop (V2, POST com os ids no corpo).
     *
     * Erro PARCIAL vem em `data.error` com HTTP 200 e `code` externo 0 —
     * checar so' o envelope engana.
     *
     * @param  array<int, int>  $tokoPids
     */
    public function mapTokoProduct(array $tokoPids, string $version = '202607'): TokoProductMapperResponseDTO
    {
        $response = $this->makeRequest(
            HttpMethod::POST,
            "/affiliate_creator/{$version}/map_toko_product",
            [],
            ['toko_pids' => $tokoPids],
        );

        return TokoProductMapperResponseDTO::fromArray($response['data'] ?? []);
    }

    // ------------------------------------------------------------- INTERNOS

    /**
     * Corpo comum dos dois geradores de sharing link.
     *
     * @param  array<int, string>  $materialIds
     * @return array<string, mixed>
     */
    private function sharingLinkBody(
        array $materialIds,
        string $materialType,
        ?string $promotionCampaignSchema,
        ?string $campaignId,
        ?string $linkType,
    ): array {
        if (count($materialIds) > self::MAX_SHARING_LINK_MATERIALS) {
            throw new InvalidArgumentException(sprintf(
                'Max %d materiais por lote (recebeu %d).',
                self::MAX_SHARING_LINK_MATERIALS,
                count($materialIds),
            ));
        }

        if ($materialType === 'CAMPAIGN' && $promotionCampaignSchema === null) {
            throw new InvalidArgumentException('promotion_campaign_schema e obrigatorio quando type = CAMPAIGN.');
        }

        return array_filter([
            'material' => array_filter([
                'ids' => $materialIds,
                'type' => $materialType,
                'promotion_campaign_schema' => $promotionCampaignSchema,
            ], fn ($v) => $v !== null),
            'campaign_id' => $campaignId,
            'link_type' => $linkType,
        ], fn ($v) => $v !== null);
    }

    /**
     * POST multipart assinado.
     *
     * A assinatura do TikTok NAO cobre corpo multipart, entao a query e'
     * assinada com body null. E o cliente vem do `makeMultipart()` porque o
     * cliente padrao fixa `Content-Type: application/json` — o Guzzle so' gera
     * o boundary quando o header nao vem definido.
     *
     * @return array<string, mixed>
     */
    private function uploadMultipart(string $apiPath, string $fileContents, string $fileName): array
    {
        $apiPath = $this->normalizeApiPath($apiPath);
        $query = $this->buildSignedQuery($apiPath, [], null);

        $response = HttpClientFactory::makeMultipart($this->integration)
            ->attach('data', $fileContents, $fileName)
            ->post($apiPath.'?'.http_build_query($query));

        $data = $response->json() ?? [];
        if ($response->failed() || ($data['code'] ?? 0) !== 0) {
            $this->handleError($response);
        }

        return $data;
    }
}
