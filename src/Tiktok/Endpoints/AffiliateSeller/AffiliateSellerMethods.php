<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\Endpoints\AffiliateSeller;

use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Tiktok\Bases\BaseMethods;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\AffiliateAckResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\AffiliateOrderSearchResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\AffiliateProductSearchResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\CompassTaskCreateResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\CompassTaskFileResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\CompassTaskListResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\ConversationCreateResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\ConversationListResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\ConversationMessageListResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\CreatorContentDetailResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\CreatorMarketplaceFiltersResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\CreatorOutreachQuotaResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\CreatorOutreachRecordsResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\CreatorPromotionDetailResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\InboxCategoryCountsResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\LatestUnreadMessagesResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\MarkConversationReadResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\MarketplaceCreatorPerformanceResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\MarketplaceCreatorSearchResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\MessageImageUploadResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\OpenCollaborationCreateResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\OpenCollaborationRemoveResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\OpenCollaborationSearchResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\OpenCollaborationSettingsResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\ProductPromotionLinkResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\SampleApplicationSearchResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\SampleFulfillmentSearchResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\SampleRequestDeeplinkResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\SampleRuleListResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\SendImMessageResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\TargetCollaborationConflictCheckResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\TargetCollaborationConflictResolveResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\TargetCollaborationCreateResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\TargetCollaborationDetailResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\TargetCollaborationLinkResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\TargetCollaborationSearchResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller\TargetCollaborationUpdateResponseDTO;
use SistemAtc\Marketplaces\Tiktok\Support\HttpClientFactory;

/**
 * Affiliate Seller API do TikTok Shop (`/affiliate_seller/{version}/...`).
 *
 * POR QUE ESTE GRUPO IMPORTA PRO FINANCEIRO: comissao de afiliado e' uma das
 * maiores deducoes do repasse do canal — no extrato ela chega agregada, como
 * `affiliate_commission_amount` e `affiliate_ads_commission_amount`. E' aqui
 * que se descobre a ORIGEM de cada centavo: qual creator, qual colaboracao ou
 * campanha, e a que taxa. `searchAffiliateOrders` e' o endpoint que fecha a
 * conta linha a linha.
 *
 * ARMADILHAS QUE ATRAVESSAM O GRUPO INTEIRO:
 *
 * 1. TRES CONVENCOES DE PORCENTAGEM CONVIVEM. O padrao sao centesimos de por
 *    cento (`3587` = 35,87%), mas `SampleApplication::$commissionRate` e'
 *    FRACAO em string ("0.1" = 10%) e as distribuicoes de creator sao fracao
 *    tambem ("0.3705" = 37,05%). Nunca reaproveite o mesmo parser.
 * 2. DINHEIRO E' STRING, sempre. Idem taxas que a API manda entre aspas.
 * 3. TEMPO E' EPOCH EM SEGUNDOS — exceto `CreatorPromotionProductBaseInfo` e
 *    `CreatorOutreachQuota`, que usam MILISSEGUNDOS.
 * 4. SUCESSO PARCIAL E' O NORMAL. create/update de convite dirigido, resolucao
 *    de conflito e `markConversationsRead` devolvem `code=0` com listas de
 *    falha dentro. Quem so' olha o codigo acha que deu tudo certo.
 * 5. Cinco rotas mais novas embrulham o payload em `data.data`.
 *
 * DOIS ERROS DE DIGITACAO ESTAO NA API, nao aqui: o path
 * `/202412/conversatons/read` (sem o segundo `i`) e o campo
 * `creator_inivited_count` da busca de convites dirigidos.
 */
class AffiliateSellerMethods extends BaseMethods
{
    /** Teto de conversas por chamada de `markConversationsRead`. */
    private const MAX_CONVERSATIONS_PER_READ = 20;

    /** Teto de produtos num convite dirigido. */
    private const MAX_TARGET_COLLABORATION_PRODUCTS = 100;

    /** Teto de creators num convite dirigido. */
    private const MAX_TARGET_COLLABORATION_CREATORS = 50;

    // ─────────────────────────────────────────────────────────────────────
    // Pedidos de afiliado — a origem da comissao do extrato
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Pedidos de afiliado numa janela de criacao —
     * `POST /affiliate_seller/202410/orders/search`.
     *
     * O TikTok so' guarda 3 MESES por requisicao. Sem `createTimeGe`/`Lt` ele
     * assume os ultimos 3 meses; com so' um dos dois, completa o outro
     * (`Lt` = agora, `Ge` = inicio da loja).
     *
     * `totalCount` conta SKU-orders, nao pedidos.
     *
     * @param  string|null  $programId  campanha do parceiro/agencia (TAP)
     */
    public function searchAffiliateOrders(
        ?int $createTimeGe = null,
        ?int $createTimeLt = null,
        ?string $programId = null,
        int $pageSize = 20,
        string $pageToken = '',
        string $version = '202410',
    ): AffiliateOrderSearchResponseDTO {
        $body = array_filter([
            'create_time_ge' => $createTimeGe,
            'create_time_lt' => $createTimeLt,
            'program_id' => $programId,
        ], fn ($v) => $v !== null);

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/affiliate_seller/{$version}/orders/search",
            $this->pagedQuery($pageSize, $pageToken),
            $body,
        );

        return AffiliateOrderSearchResponseDTO::fromArray($response['data'] ?? []);
    }

    // ─────────────────────────────────────────────────────────────────────
    // Colaboracao aberta (comissao publica a qualquer creator)
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Poe um produto na colaboracao aberta —
     * `POST /affiliate_seller/202412/open_collaborations`.
     *
     * @param  int  $commissionRate  centesimos de %: 3587 = 35,87%. Minimo 100.
     */
    public function createOpenCollaboration(
        string $productId,
        int $commissionRate,
        string $version = '202412',
    ): OpenCollaborationCreateResponseDTO {
        $response = $this->makeRequest(
            HttpMethod::POST,
            "/affiliate_seller/{$version}/open_collaborations",
            [],
            ['product_id' => $productId, 'commission_rate' => $commissionRate],
        );

        return OpenCollaborationCreateResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Busca colaboracoes abertas —
     * `POST /affiliate_seller/202412/open_collaborations/search`.
     *
     * As ja removidas E expiradas nao voltam; as em `TERMINATING` voltam.
     *
     * @param  string|null  $keywordType  PRODUCT_ID | PRODUCT_NAME
     */
    public function searchOpenCollaborations(
        ?string $keywordType = null,
        ?string $keyword = null,
        ?string $topLevelCategoryId = null,
        int $pageSize = 20,
        string $pageToken = '',
        ?string $sortField = null,
        ?string $sortOrder = null,
        string $version = '202412',
    ): OpenCollaborationSearchResponseDTO {
        $query = $this->pagedQuery($pageSize, $pageToken);
        if ($sortField !== null) {
            $query['sort_field'] = $sortField;
        }
        if ($sortOrder !== null) {
            $query['sort_order'] = $sortOrder;
        }

        $body = array_filter([
            'keyword_type' => $keywordType,
            'keyword' => $keyword,
            'top_level_category_id' => $topLevelCategoryId,
        ], fn ($v) => $v !== null);

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/affiliate_seller/{$version}/open_collaborations/search",
            $query,
            $body,
        );

        return OpenCollaborationSearchResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Tira um produto da colaboracao aberta —
     * `POST /affiliate_seller/202409/open_collaborations/products/{product_id}`.
     *
     * NAO e' imediato: veja `terminatedEffectiveTime` (em geral 00:00 do dia
     * seguinte). Ate la o creator continua promovendo e gerando comissao.
     */
    public function removeOpenCollaboration(
        string $productId,
        string $version = '202409',
    ): OpenCollaborationRemoveResponseDTO {
        $response = $this->makeRequest(
            HttpMethod::POST,
            "/affiliate_seller/{$version}/open_collaborations/products/{$productId}",
            [],
            [],
        );

        return OpenCollaborationRemoveResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Tira UM creator da colaboracao aberta de um produto —
     * `POST /affiliate_seller/202508/open_collaborations/{id}/remove_creator`.
     */
    public function removeCreatorFromOpenCollaboration(
        string $openCollaborationId,
        string $creatorUserOpenId,
        string $productId,
        string $version = '202508',
    ): AffiliateAckResponseDTO {
        $response = $this->makeRequest(
            HttpMethod::POST,
            "/affiliate_seller/{$version}/open_collaborations/{$openCollaborationId}/remove_creator",
            [],
            ['creator_user_open_id' => $creatorUserOpenId, 'product_id' => $productId],
        );

        return AffiliateAckResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Configuracao de entrada automatica na colaboracao aberta —
     * `GET /affiliate_seller/202409/open_collaboration_settings`.
     */
    public function getOpenCollaborationSettings(
        string $version = '202409',
    ): OpenCollaborationSettingsResponseDTO {
        $response = $this->makeRequest(
            HttpMethod::GET,
            "/affiliate_seller/{$version}/open_collaboration_settings",
        );

        return OpenCollaborationSettingsResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Liga/desliga a entrada automatica de produtos na colaboracao aberta —
     * `POST /affiliate_seller/202405/open_collaboration_settings`.
     *
     * Ligar aplica a comissao a TODO produto nao-afiliado que ja existe, e aos
     * futuros. Desligar so' vale pros futuros: o que ja entrou continua la.
     *
     * @param  int  $commissionRate  centesimos de %, faixa [100, 8000]
     */
    public function editOpenCollaborationSettings(
        bool $enable,
        int $commissionRate,
        string $version = '202405',
    ): AffiliateAckResponseDTO {
        $response = $this->makeRequest(
            HttpMethod::POST,
            "/affiliate_seller/{$version}/open_collaboration_settings",
            [],
            ['auto_add_product' => ['enable' => $enable, 'commission_rate' => $commissionRate]],
        );

        return AffiliateAckResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Regras de amostra gratis por produto —
     * `GET /affiliate_seller/202410/open_collaborations/sample_rules`.
     *
     * @param  array<int, string>  $productIds
     */
    public function getOpenCollaborationSampleRules(
        array $productIds,
        string $version = '202410',
    ): SampleRuleListResponseDTO {
        if ($productIds === []) {
            return SampleRuleListResponseDTO::fromArray([]);
        }

        $response = $this->makeRequest(
            HttpMethod::GET,
            "/affiliate_seller/{$version}/open_collaborations/sample_rules",
            ['product_ids' => implode(',', $productIds)],
        );

        return SampleRuleListResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Cria/ativa ou desativa a regra de amostra de UM produto —
     * `POST /affiliate_seller/202410/open_collaborations/sample_rules`.
     *
     * E' 1 regra por produto e a ULTIMA chamada vence — nao ha merge. Com
     * `DEACTIVATE` basta o `productId`; com `ACTIVATE` o `$sampleRule` inteiro
     * precisa vir, senao o que ficou de fora e' apagado.
     *
     * @param  array<string, mixed>  $sampleRule  sample_quota, is_sample_time_unlimited, start_time, end_time, thresholds
     */
    public function editOpenCollaborationSampleRule(
        string $productId,
        string $activateStatus = 'ACTIVATE',
        array $sampleRule = [],
        string $version = '202410',
    ): AffiliateAckResponseDTO {
        $response = $this->makeRequest(
            HttpMethod::POST,
            "/affiliate_seller/{$version}/open_collaborations/sample_rules",
            [],
            [
                'product_id' => $productId,
                'sample_rule' => $sampleRule + ['activate_status' => $activateStatus],
            ],
        );

        return AffiliateAckResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Quanto conteudo cada creator produziu pro produto na colaboracao aberta —
     * `GET /affiliate_seller/202508/open_collaborations/creator_content_details`.
     */
    public function getOpenCollaborationCreatorContentDetails(
        string $productId,
        int $pageSize = 20,
        string $pageToken = '',
        string $version = '202508',
    ): CreatorContentDetailResponseDTO {
        $response = $this->makeRequest(
            HttpMethod::GET,
            "/affiliate_seller/{$version}/open_collaborations/creator_content_details",
            $this->pagedQuery($pageSize, $pageToken) + ['product_id' => $productId],
        );

        return CreatorContentDetailResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Vitrine de produtos elegiveis a colaboracao aberta —
     * `POST /affiliate_seller/202405/open_collaborations/products/search`.
     *
     * `page_size` aqui vai so' ate 20 (o resto do grupo aceita 100).
     *
     * @param  array<string, mixed>  $filters  title_keywords, sales_price_range, category, commission_rate_range
     */
    public function searchOpenCollaborationProducts(
        array $filters = [],
        int $pageSize = 20,
        string $pageToken = '',
        ?string $sortField = null,
        ?string $sortOrder = null,
        string $version = '202405',
    ): AffiliateProductSearchResponseDTO {
        $query = $this->pagedQuery(min($pageSize, 20), $pageToken);
        if ($sortField !== null) {
            $query['sort_field'] = $sortField;
        }
        if ($sortOrder !== null) {
            $query['sort_order'] = $sortOrder;
        }

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/affiliate_seller/{$version}/open_collaborations/products/search",
            $query,
            $filters,
        );

        return AffiliateProductSearchResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Link de divulgacao de um produto —
     * `POST /affiliate_seller/202405/products/{product_id}/promotion_link/generate`.
     */
    public function generateProductPromotionLink(
        string $productId,
        string $version = '202405',
    ): ProductPromotionLinkResponseDTO {
        $response = $this->makeRequest(
            HttpMethod::POST,
            "/affiliate_seller/{$version}/products/{$productId}/promotion_link/generate",
            [],
            [],
        );

        return ProductPromotionLinkResponseDTO::fromArray($response['data'] ?? []);
    }

    // ─────────────────────────────────────────────────────────────────────
    // Convite dirigido (target collaboration)
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Cria um convite dirigido a creators escolhidos —
     * `POST /affiliate_seller/202508/target_collaborations`.
     *
     * SUCESSO PARCIAL: com `code=0` o retorno ainda pode trazer
     * `targetCollaborationConflicts`, `invalidOpenIdList` e
     * `invalidProductIdList` — creators/produtos que NAO entraram. Rode
     * `checkTargetCollaborationConflicts` antes pra nao descobrir depois.
     *
     * @param  array<int, array<string, mixed>>  $products  id + target_commission_rate (+ shop_ads_commission_rate)
     * @param  array<int, string>  $creatorUserOpenIds
     * @param  array<string, string>  $sellerContactInfo  email, phone_number, whatsapp, telegram, line
     */
    public function createTargetCollaboration(
        string $name,
        int $endTime,
        array $products,
        array $creatorUserOpenIds,
        array $sellerContactInfo,
        bool $hasFreeSample = false,
        bool $isSampleApprovalExempt = false,
        ?string $message = null,
        ?string $preferredContentType = null,
        string $version = '202508',
    ): TargetCollaborationCreateResponseDTO {
        $this->assertTargetCollaborationSize($products, $creatorUserOpenIds);

        $body = array_filter([
            'name' => $name,
            // A doc declara `end_time` como string apesar de ser epoch.
            'end_time' => (string) $endTime,
            'message' => $message,
            'products' => $products,
            'creator_user_open_ids' => array_values($creatorUserOpenIds),
            'seller_contact_info' => $sellerContactInfo,
            'free_sample_rule' => [
                'has_free_sample' => $hasFreeSample,
                'is_sample_approval_exempt' => $isSampleApprovalExempt,
            ],
            'preferred_content_type' => $preferredContentType,
        ], fn ($v) => $v !== null);

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/affiliate_seller/{$version}/target_collaborations",
            [],
            $body,
        );

        return TargetCollaborationCreateResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Reescreve um convite dirigido —
     * `POST /affiliate_seller/202508/target_collaborations/{id}`.
     *
     * E' SUBSTITUICAO, nao patch: quem sair das listas e' removido. Produto
     * removido que ja esta na vitrine de algum creator so' cai as 00:00 do dia
     * seguinte; AUMENTO de comissao vale na hora, REDUCAO tambem espera 00:00.
     *
     * Leia `updateFailed` mesmo com `code=0` — parte do update cai no chao em
     * silencio.
     *
     * @param  array<int, array<string, mixed>>  $products  id + commission_rate (+ target_ad_commission_rate)
     * @param  array<int, string>  $creatorUserOpenIds
     * @param  array<string, string>  $sellerContactInfo
     */
    public function updateTargetCollaboration(
        string $targetCollaborationId,
        string $name,
        int $endTime,
        array $products,
        array $creatorUserOpenIds,
        array $sellerContactInfo,
        bool $hasFreeSample = false,
        bool $isSampleApprovalExempt = false,
        string $version = '202508',
    ): TargetCollaborationUpdateResponseDTO {
        $this->assertTargetCollaborationSize($products, $creatorUserOpenIds);

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/affiliate_seller/{$version}/target_collaborations/{$targetCollaborationId}",
            [],
            [
                'name' => $name,
                'end_time' => (string) $endTime,
                'products' => $products,
                'creator_user_open_ids' => array_values($creatorUserOpenIds),
                'seller_contact_info' => $sellerContactInfo,
                'free_sample_rule' => [
                    'has_free_sample' => $hasFreeSample,
                    'is_sample_approval_exempt' => $isSampleApprovalExempt,
                ],
            ],
        );

        return TargetCollaborationUpdateResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Encerra um convite dirigido —
     * `POST /affiliate_seller/202409/target_collaborations/{id}`.
     */
    public function removeTargetCollaboration(
        string $targetCollaborationId,
        string $version = '202409',
    ): AffiliateAckResponseDTO {
        $response = $this->makeRequest(
            HttpMethod::POST,
            "/affiliate_seller/{$version}/target_collaborations/{$targetCollaborationId}",
            [],
            [],
        );

        return AffiliateAckResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Link compartilhavel do convite dirigido —
     * `POST /affiliate_seller/202509/target_collaboration/{id}/link`.
     *
     * O path e' `target_collaboration` no SINGULAR: unica rota do grupo assim.
     */
    public function generateTargetCollaborationLink(
        string $targetCollaborationId,
        string $version = '202509',
    ): TargetCollaborationLinkResponseDTO {
        $response = $this->makeRequest(
            HttpMethod::POST,
            "/affiliate_seller/{$version}/target_collaboration/{$targetCollaborationId}/link",
            [],
            [],
        );

        return TargetCollaborationLinkResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Busca convites dirigidos —
     * `POST /affiliate_seller/202508/target_collaborations/search`.
     *
     * `page_size` so' aceita 20, 50 ou 100 — outro valor e' recusado.
     *
     * @param  string  $collaborationStatus  ONGOING | EXPIRING | VALID | CANCELING | COMPLETED
     * @param  array<string, string>|null  $searchParam  keyword_type + keyword
     */
    public function searchTargetCollaborations(
        string $collaborationStatus,
        ?array $searchParam = null,
        ?string $creatorAcceptStatus = null,
        ?string $freeSampleSetting = null,
        ?string $creatorUserOpenId = null,
        int $pageSize = 20,
        string $pageToken = '',
        string $version = '202508',
    ): TargetCollaborationSearchResponseDTO {
        if (! in_array($pageSize, [20, 50, 100], true)) {
            throw new InvalidArgumentException('page_size aceita apenas 20, 50 ou 100 nesta rota.');
        }

        $body = array_filter([
            'collaboration_status' => $collaborationStatus,
            'search_param' => $searchParam,
            'creator_accept_status' => $creatorAcceptStatus,
            'free_sample_setting' => $freeSampleSetting,
            'creator_user_open_id' => $creatorUserOpenId,
        ], fn ($v) => $v !== null);

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/affiliate_seller/{$version}/target_collaborations/search",
            $this->pagedQuery($pageSize, $pageToken),
            $body,
        );

        return TargetCollaborationSearchResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Detalhe de um convite dirigido (produtos + creators) —
     * `GET /affiliate_seller/202508/target_collaborations/{id}`.
     */
    public function getTargetCollaboration(
        string $targetCollaborationId,
        string $version = '202508',
    ): TargetCollaborationDetailResponseDTO {
        $response = $this->makeRequest(
            HttpMethod::GET,
            "/affiliate_seller/{$version}/target_collaborations/{$targetCollaborationId}",
        );

        return TargetCollaborationDetailResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Confere se os pares creator x produto ja estao em outro convite —
     * `POST /affiliate_seller/202605/target_collaborations/conflicts/check`.
     *
     * Rode ANTES de `createTargetCollaboration`: o create nao falha por
     * conflito, ele apenas deixa o par de fora.
     *
     * @param  array<int, string>  $creatorOpenIds
     * @param  array<int, string>  $productIds
     */
    public function checkTargetCollaborationConflicts(
        array $creatorOpenIds,
        array $productIds,
        string $version = '202605',
    ): TargetCollaborationConflictCheckResponseDTO {
        $response = $this->makeRequest(
            HttpMethod::POST,
            "/affiliate_seller/{$version}/target_collaborations/conflicts/check",
            [],
            [
                'creator_open_id_list' => array_values($creatorOpenIds),
                'product_id_list' => array_values($productIds),
            ],
        );

        return TargetCollaborationConflictCheckResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Cancela o convite CONFLITANTE pra liberar o par —
     * `POST /affiliate_seller/202605/target_collaborations/conflicts/resolve`.
     *
     * Cada item e' (creator, colaboracao existente) — o produto nao entra.
     * Resposta parcial: leia `successItems` E `failedItems`.
     *
     * @param  array<int, array{creator_open_id: string, existing_collaboration_id: string}>  $conflictItems
     */
    public function cancelTargetCollaborationConflicts(
        array $conflictItems,
        string $version = '202605',
    ): TargetCollaborationConflictResolveResponseDTO {
        $response = $this->makeRequest(
            HttpMethod::POST,
            "/affiliate_seller/{$version}/target_collaborations/conflicts/resolve",
            [],
            ['conflict_items' => array_values($conflictItems)],
        );

        return TargetCollaborationConflictResolveResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * O que um creator esta promovendo dentro do convite dirigido —
     * `POST /affiliate_seller/202608/target_collaborations/creator_promotion_details/query`.
     *
     * Duas pegadinhas no retorno: os tempos de vigencia de comissao vem em
     * MILISSEGUNDOS e `productStatus` e' codigo numerico ("1".."6"), nao o
     * enum textual usado nas outras rotas.
     *
     * @param  array<string, mixed>|null  $productFilter  ['added_types' => ['ALL'|'ADDED'|'NOT_ADDED']]
     */
    public function queryCreatorPromotionDetails(
        string $creatorUserOpenId,
        string $invitationGroupId,
        ?string $query = null,
        ?string $queryType = null,
        ?string $invitationId = null,
        ?array $productFilter = null,
        string $version = '202608',
    ): CreatorPromotionDetailResponseDTO {
        $body = array_filter([
            'creator_user_open_id' => $creatorUserOpenId,
            'invitation_group_id' => $invitationGroupId,
            'query' => $query,
            'query_type' => $queryType,
            'invitation_id' => $invitationId,
            'product_filter' => $productFilter,
        ], fn ($v) => $v !== null);

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/affiliate_seller/{$version}/target_collaborations/creator_promotion_details/query",
            [],
            $body,
        );

        return CreatorPromotionDetailResponseDTO::fromArray($response['data'] ?? []);
    }

    // ─────────────────────────────────────────────────────────────────────
    // Amostras gratis
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Pedidos de amostra feitos por creators —
     * `POST /affiliate_seller/202508/sample_applications/search`.
     *
     * Cuidado com o filtro `creator_user_oepn_id`: o erro de digitacao esta na
     * API. E `commissionRate` no retorno e' FRACAO ("0.1" = 10%), nao
     * centesimos de por cento como no resto do grupo.
     *
     * @param  string|null  $targetCollaborationId  vai no campo `target_collabration_id` (typo da API)
     */
    public function searchSampleApplications(
        ?string $status = null,
        ?string $productId = null,
        ?string $title = null,
        ?string $creatorUserOpenId = null,
        ?string $username = null,
        ?string $targetCollaborationId = null,
        ?string $orderId = null,
        int $pageSize = 20,
        string $pageToken = '',
        string $version = '202508',
    ): SampleApplicationSearchResponseDTO {
        $body = array_filter([
            'status' => $status,
            'product_id' => $productId,
            'title' => $title,
            // typos da API: "oepn" e "collabration". Corrigir = filtro ignorado.
            'creator_user_oepn_id' => $creatorUserOpenId,
            'username' => $username,
            'target_collabration_id' => $targetCollaborationId,
            'order_id' => $orderId,
        ], fn ($v) => $v !== null);

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/affiliate_seller/{$version}/sample_applications/search",
            $this->pagedQuery(min($pageSize, 50), $pageToken),
            $body,
        );

        return SampleApplicationSearchResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Conteudo que o creator postou depois de receber a amostra —
     * `POST /affiliate_seller/202409/sample_applications/{id}/fulfillments/search`.
     *
     * Nao e' paginado: devolve tudo de uma vez.
     */
    public function searchSampleApplicationFulfillments(
        string $applicationId,
        ?string $contentFormat = null,
        string $version = '202409',
    ): SampleFulfillmentSearchResponseDTO {
        $body = $contentFormat === null ? [] : ['content_format' => $contentFormat];

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/affiliate_seller/{$version}/sample_applications/{$applicationId}/fulfillments/search",
            [],
            $body,
        );

        return SampleFulfillmentSearchResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Aprova ou recusa um pedido de amostra —
     * `POST /affiliate_seller/202507/sample_applications/{id}/review`.
     *
     * `rejectReason` e' OBRIGATORIO quando `reviewResult=REJECT`.
     *
     * @param  string  $reviewResult  APPROVE | REJECT
     * @param  string|null  $rejectReason  NOT_MATCH | OFFLINE | OUT_OF_STOCK | OTHER
     */
    public function reviewSampleApplication(
        string $applicationId,
        string $reviewResult,
        ?string $rejectReason = null,
        string $version = '202507',
    ): AffiliateAckResponseDTO {
        if ($reviewResult === 'REJECT' && $rejectReason === null) {
            throw new InvalidArgumentException('reject_reason e obrigatorio quando review_result = REJECT.');
        }

        $body = array_filter([
            'review_result' => $reviewResult,
            'reject_reason' => $rejectReason,
        ], fn ($v) => $v !== null);

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/affiliate_seller/{$version}/sample_applications/{$applicationId}/review",
            [],
            $body,
        );

        return AffiliateAckResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Deeplink pro creator pedir amostra de um SKU —
     * `GET /affiliate_seller/202512/sample_applications/deeplink`.
     *
     * Devolve `snssdk1180://`, que so' abre dentro do app do TikTok.
     * `validDays`: minimo 1, padrao 7, maximo 14.
     */
    public function getSampleRequestDeeplink(
        string $productId,
        string $skuId,
        ?string $campaignId = null,
        ?string $collaborationId = null,
        ?int $validDays = null,
        string $version = '202512',
    ): SampleRequestDeeplinkResponseDTO {
        $query = array_filter([
            'product_id' => $productId,
            'sku_id' => $skuId,
            'campaign_id' => $campaignId,
            'collaboration_id' => $collaborationId,
            'valid_days' => $validDays,
        ], fn ($v) => $v !== null);

        $response = $this->makeRequest(
            HttpMethod::GET,
            "/affiliate_seller/{$version}/sample_applications/deeplink",
            $query,
        );

        return SampleRequestDeeplinkResponseDTO::fromArray($response['data'] ?? []);
    }

    // ─────────────────────────────────────────────────────────────────────
    // Creator marketplace
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Busca creators no marketplace —
     * `POST /affiliate_seller/202608/marketplace_creators/search`.
     *
     * `page_size` so' aceita 12 ou 20.
     *
     * PAGINAR SEM `searchKey` E' ARMADILHA: devolva na chamada seguinte o
     * `searchKey` que veio na primeira, senao a pagina 2 sai de um rank
     * recalculado e repete/pula creators.
     *
     * O corpo de filtros e' grande e muda por pais — descubra o que vale com
     * `getMarketplaceCreatorSearchFilters` em vez de fixar constante.
     *
     * @param  array<string, mixed>  $filters  keyword, follower_demographics, gmv_ranges, category, content_performance, affiliate_data, advanced_filters, data_groups
     */
    public function searchMarketplaceCreators(
        array $filters = [],
        ?string $searchKey = null,
        int $pageSize = 20,
        string $pageToken = '',
        string $version = '202608',
    ): MarketplaceCreatorSearchResponseDTO {
        if (! in_array($pageSize, [12, 20], true)) {
            throw new InvalidArgumentException('page_size aceita apenas 12 ou 20 nesta rota.');
        }

        if ($searchKey !== null) {
            $filters['search_key'] = $searchKey;
        }

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/affiliate_seller/{$version}/marketplace_creators/search",
            $this->pagedQuery($pageSize, $pageToken),
            $filters,
        );

        return MarketplaceCreatorSearchResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Ficha completa de desempenho de um creator —
     * `GET /affiliate_seller/202608/marketplace_creators/{creator_user_id}`.
     *
     * Sem `dataGroups` vem tudo. Se o creator nao autorizou dado exato, os
     * campos precisos somem e sobram os `*Range` (so' em mercado US).
     *
     * @param  array<int, string>  $dataGroups  BASIC_CREATOR_INFORMATION | CONTENT_PERFORMANCE | FOLLOWER_INFORMATION | COLLABORATION | SALES_AND_GMV
     */
    public function getMarketplaceCreatorPerformance(
        string $creatorUserId,
        array $dataGroups = [],
        string $version = '202608',
    ): MarketplaceCreatorPerformanceResponseDTO {
        $query = $dataGroups === [] ? [] : ['data_groups' => implode(',', $dataGroups)];

        $response = $this->makeRequest(
            HttpMethod::GET,
            "/affiliate_seller/{$version}/marketplace_creators/{$creatorUserId}",
            $query,
        );

        return MarketplaceCreatorPerformanceResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Filtros e ordenacoes disponiveis pra busca de creators —
     * `POST /affiliate_seller/202608/marketplace_creators/search/filter`.
     *
     * O catalogo muda por pais e por conta; lista vazia significa "nao liberado
     * pra voce", nao erro.
     *
     * @param  array<int, string>  $optionTypes  OPTION_VERTICAL_PRO | OPTION_LANGUAGE | OPTION_CREATOR_LEVEL | OPTION_SORTER | OPTION_BRAND
     */
    public function getMarketplaceCreatorSearchFilters(
        array $optionTypes = [],
        string $version = '202608',
    ): CreatorMarketplaceFiltersResponseDTO {
        $body = $optionTypes === []
            ? []
            : ['options' => array_map(fn (string $t) => ['option_type' => $t], array_values($optionTypes))];

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/affiliate_seller/{$version}/marketplace_creators/search/filter",
            [],
            $body,
        );

        return CreatorMarketplaceFiltersResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Cota semanal de abordagem a creators —
     * `GET /affiliate_seller/202607/creator_outreach/quota`.
     *
     * Contadores e timestamps vem como STRING, e os tempos em MILISSEGUNDOS.
     */
    public function getWeeklyCreatorOutreachQuota(
        string $version = '202607',
    ): CreatorOutreachQuotaResponseDTO {
        $response = $this->makeRequest(
            HttpMethod::GET,
            "/affiliate_seller/{$version}/creator_outreach/quota",
        );

        return CreatorOutreachQuotaResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Creators conectados no periodo da cota —
     * `POST /affiliate_seller/202608/creator_outreach/records/query`.
     *
     * UNICA rota do grupo paginada por INDICE (base 1), nao por cursor.
     */
    public function getWeeklyCreatorOutreachRecords(
        int $pageIndex = 1,
        int $pageSize = 20,
        string $version = '202608',
    ): CreatorOutreachRecordsResponseDTO {
        if ($pageIndex < 1) {
            throw new InvalidArgumentException('page_index e base 1 nesta rota.');
        }

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/affiliate_seller/{$version}/creator_outreach/records/query",
            [],
            ['page_index' => $pageIndex, 'page_size' => min($pageSize, 50)],
        );

        return CreatorOutreachRecordsResponseDTO::fromArray($response['data'] ?? []);
    }

    // ─────────────────────────────────────────────────────────────────────
    // Compass — exportacao offline de analytics de afiliado
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Enfileira uma exportacao do Compass —
     * `POST /affiliate_seller/202603/compass/offline_task`.
     *
     * ASSINCRONO: guarde o id, acompanhe com `getCompassTasks` ate
     * `status=SUCCEEDED` e so' entao chame `downloadCompassTaskFile`.
     *
     * `endDay` e' YYYYMMDD no fuso LOCAL da regiao da loja (BR = UTC-3), nao
     * em UTC — errar o fuso desloca o recorte em um dia.
     */
    public function createCompassExportTask(
        ?string $moduleType = null,
        ?string $windowType = null,
        ?int $endDay = null,
        ?string $planType = null,
        string $version = '202603',
    ): CompassTaskCreateResponseDTO {
        $body = array_filter([
            'module_type' => $moduleType,
            'window_type' => $windowType,
            'end_day' => $endDay,
            'plan_type' => $planType,
        ], fn ($v) => $v !== null);

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/affiliate_seller/{$version}/compass/offline_task",
            [],
            $body,
        );

        return CompassTaskCreateResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Exportacoes do Compass e o estado de cada uma —
     * `GET /affiliate_seller/202603/compass/offline_tasks`.
     *
     * @param  string  $docType  CREATOR | BASE
     */
    public function getCompassTasks(
        string $docType,
        string $version = '202603',
    ): CompassTaskListResponseDTO {
        $response = $this->makeRequest(
            HttpMethod::GET,
            "/affiliate_seller/{$version}/compass/offline_tasks",
            ['doc_type' => $docType],
        );

        return CompassTaskListResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Baixa o arquivo de uma exportacao —
     * `GET /affiliate_seller/202603/compass/offline_tasks/{task_id}/file`.
     *
     * O conteudo vem INTEIRO em base64 dentro do JSON (nao ha URL). Decodifique
     * direto pra disco: planilha grande vira resposta grande.
     */
    public function downloadCompassTaskFile(
        string $taskId,
        string $version = '202603',
    ): CompassTaskFileResponseDTO {
        $response = $this->makeRequest(
            HttpMethod::GET,
            "/affiliate_seller/{$version}/compass/offline_tasks/{$taskId}/file",
        );

        return CompassTaskFileResponseDTO::fromArray($response['data'] ?? []);
    }

    // ─────────────────────────────────────────────────────────────────────
    // Conversas / IM com creator
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Abre (ou recupera) a conversa com um creator —
     * `POST /affiliate_seller/202508/conversations`.
     *
     * Idempotente: se ja existe, devolve a mesma com `isNew=false`.
     * `onlyNeedConversationId=true` (padrao da API) traz SO' o id.
     */
    public function createConversation(
        string $creatorOpenId,
        bool $onlyNeedConversationId = true,
        string $version = '202508',
    ): ConversationCreateResponseDTO {
        $response = $this->makeRequest(
            HttpMethod::POST,
            "/affiliate_seller/{$version}/conversations",
            [],
            [
                'creator_open_id' => $creatorOpenId,
                'only_need_conversation_id' => $onlyNeedConversationId,
            ],
        );

        return ConversationCreateResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Caixa de entrada de conversas com creators —
     * `GET /affiliate_seller/202412/conversations`. Max 50 por pagina.
     *
     * Com `onlyNeedConversationId=true` (padrao da API) so' o `id` vem
     * preenchido — passe `false` pra receber username, avatar e contadores.
     *
     * @param  string|null  $conversationStatus  ALL | UNREAD | UNREPLIED | STARRED | ARCHIVED
     */
    public function getConversationList(
        int $pageSize = 20,
        string $pageToken = '',
        bool $onlyNeedConversationId = true,
        ?string $conversationStatus = null,
        string $version = '202412',
    ): ConversationListResponseDTO {
        $query = $this->pagedQuery(min($pageSize, 50), $pageToken)
            + ['only_need_conversation_id' => $onlyNeedConversationId ? 'true' : 'false'];

        if ($conversationStatus !== null) {
            $query['conversation_status'] = $conversationStatus;
        }

        $response = $this->makeRequest(
            HttpMethod::GET,
            "/affiliate_seller/{$version}/conversations",
            $query,
        );

        return ConversationListResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Historico de uma conversa —
     * `GET /affiliate_seller/202412/conversation/{conversation_id}/messages`.
     *
     * O path e' `conversation` no SINGULAR aqui e no PLURAL no envio — nao
     * unifique. Max 20 por pagina.
     */
    public function getConversationMessages(
        string $conversationId,
        int $pageSize = 20,
        string $pageToken = '',
        string $version = '202412',
    ): ConversationMessageListResponseDTO {
        $response = $this->makeRequest(
            HttpMethod::GET,
            "/affiliate_seller/{$version}/conversation/{$conversationId}/messages",
            $this->pagedQuery(min($pageSize, 20), $pageToken),
        );

        return ConversationMessageListResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Manda mensagem pro creator —
     * `POST /affiliate_seller/202412/conversations/{conversation_id}/messages`.
     *
     * `$content` e' JSON SERIALIZADO EM STRING e o shape depende do tipo:
     * TEXT `{"content":"oi"}`, PRODUCT_CARD `{"product_id":"123"}`,
     * IMAGE `{"url":...,"width":...,"height":...}` (url vinda de
     * `uploadMessageImageV2`). CRM_TEXT_WITH_PRODUCTS_CARD aceita no maximo
     * 5 produtos.
     */
    public function sendImMessage(
        string $conversationId,
        string $msgType,
        string $content,
        string $version = '202412',
    ): SendImMessageResponseDTO {
        $response = $this->makeRequest(
            HttpMethod::POST,
            "/affiliate_seller/{$version}/conversations/{$conversationId}/messages",
            [],
            ['msg_type' => $msgType, 'content' => $content],
        );

        return SendImMessageResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Mensagens nao lidas do ULTIMO MINUTO —
     * `GET /affiliate_seller/202412/conversations/messages/list/newest`.
     *
     * A janela e' de 1 minuto e nao ha webhook de IM de afiliado: quem chama de
     * hora em hora nao ve nada. Use polling curto ou aceite perder mensagem e
     * varra `getConversationList(conversationStatus: 'UNREAD')`.
     */
    public function getLatestUnreadMessages(
        string $version = '202412',
    ): LatestUnreadMessagesResponseDTO {
        $response = $this->makeRequest(
            HttpMethod::GET,
            "/affiliate_seller/{$version}/conversations/messages/list/newest",
        );

        return LatestUnreadMessagesResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Marca conversas como lidas —
     * `POST /affiliate_seller/202412/conversatons/read`.
     *
     * "conversatons" (sem o segundo `i`) e' o path REAL da API: corrigir o
     * typo quebra a chamada. Max 20 por vez; responder NAO marca como lido.
     *
     * @param  array<int, string>  $conversationIds
     */
    public function markConversationsRead(
        array $conversationIds,
        string $version = '202412',
    ): MarkConversationReadResponseDTO {
        if (count($conversationIds) > self::MAX_CONVERSATIONS_PER_READ) {
            throw new InvalidArgumentException(sprintf(
                'Max %d conversas por chamada (recebeu %d).',
                self::MAX_CONVERSATIONS_PER_READ,
                count($conversationIds),
            ));
        }

        $response = $this->makeRequest(
            HttpMethod::POST,
            "/affiliate_seller/{$version}/conversatons/read",
            [],
            ['conversation_ids' => array_values($conversationIds)],
        );

        return MarkConversationReadResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Favorita/desfavorita/arquiva uma conversa —
     * `POST /affiliate_seller/202608/conversations/{conversation_id}/status`.
     *
     * @param  string  $action  STARRED | UNSTARRED | ARCHIVED
     */
    public function updateConversationStatus(
        string $conversationId,
        string $action,
        string $version = '202608',
    ): AffiliateAckResponseDTO {
        $response = $this->makeRequest(
            HttpMethod::POST,
            "/affiliate_seller/{$version}/conversations/{$conversationId}/status",
            [],
            ['action' => $action],
        );

        return AffiliateAckResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Quantas conversas em cada aba da caixa de entrada —
     * `GET /affiliate_seller/202608/conversations/group_counts`.
     */
    public function getInboxCategoryCounts(
        string $version = '202608',
    ): InboxCategoryCountsResponseDTO {
        $response = $this->makeRequest(
            HttpMethod::GET,
            "/affiliate_seller/{$version}/conversations/group_counts",
        );

        return InboxCategoryCountsResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Sobe imagem pra usar em mensagem (rota antiga) —
     * `/affiliate_seller/202511/images/upload`. JPG/GIF/WEBP/PNG, max 10MB.
     *
     * A doc declara GET, o que e' erro dela: o corpo e' multipart, entao vai
     * POST — mesmo caso do upload de comprovante em FulfillmentMethods.
     *
     * Prefira `uploadMessageImageV2`; esta fica pra loja que ainda nao tem a
     * rota nova liberada.
     */
    public function uploadMessageImage(
        string $contents,
        string $fileName = 'image.jpg',
        string $version = '202511',
    ): MessageImageUploadResponseDTO {
        return MessageImageUploadResponseDTO::fromArray(
            $this->multipartUpload("/affiliate_seller/{$version}/images/upload", $contents, $fileName),
        );
    }

    /**
     * Sobe imagem pra usar em mensagem (rota atual) —
     * `/affiliate_seller/202608/media/upload`. JPG/GIF/WEBP/PNG, max 10MB.
     *
     * Mesma correcao de metodo (a doc diz GET; e' POST multipart). A `url` do
     * retorno, com `width`/`height`, e' o que vai no `content` de uma mensagem
     * IMAGE em `sendImMessage`.
     */
    public function uploadMessageImageV2(
        string $contents,
        string $fileName = 'image.jpg',
        string $version = '202608',
    ): MessageImageUploadResponseDTO {
        return MessageImageUploadResponseDTO::fromArray(
            $this->multipartUpload("/affiliate_seller/{$version}/media/upload", $contents, $fileName),
        );
    }

    // ─────────────────────────────────────────────────────────────────────
    // Internos
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Query de paginacao por cursor. O `page_token` e' OMITIDO na primeira
     * pagina — mandar string vazia faz o TikTok recusar em algumas rotas.
     *
     * @return array<string, mixed>
     */
    private function pagedQuery(int $pageSize, string $pageToken): array
    {
        $query = ['page_size' => $pageSize];
        if ($pageToken !== '') {
            $query['page_token'] = $pageToken;
        }

        return $query;
    }

    /**
     * @param  array<int, mixed>  $products
     * @param  array<int, string>  $creatorUserOpenIds
     */
    private function assertTargetCollaborationSize(array $products, array $creatorUserOpenIds): void
    {
        if (count($products) > self::MAX_TARGET_COLLABORATION_PRODUCTS) {
            throw new InvalidArgumentException(sprintf(
                'Max %d produtos por convite dirigido (recebeu %d).',
                self::MAX_TARGET_COLLABORATION_PRODUCTS,
                count($products),
            ));
        }

        if (count($creatorUserOpenIds) > self::MAX_TARGET_COLLABORATION_CREATORS) {
            throw new InvalidArgumentException(sprintf(
                'Max %d creators por convite dirigido (recebeu %d).',
                self::MAX_TARGET_COLLABORATION_CREATORS,
                count($creatorUserOpenIds),
            ));
        }
    }

    /**
     * POST multipart assinado.
     *
     * O BaseMethods so' fala JSON e a assinatura do TikTok NAO cobre corpo
     * multipart — a query e' assinada com body null e a requisicao sai pelo
     * httpClient com `attach()`. O cliente e' montado do zero porque o
     * HttpClientFactory chama `asJson()`, e o header de JSON sobreviveria ao
     * `attach()` e faria o TikTok recusar o multipart.
     *
     * @return array<string, mixed>
     */
    private function multipartUpload(string $apiPath, string $contents, string $fileName): array
    {
        $apiPath = $this->normalizeApiPath($apiPath);
        $query = $this->buildSignedQuery($apiPath, [], null);

        // Chamado so' pelo efeito colateral de refrescar o token.
        HttpClientFactory::make($this->integration);

        $response = Http::baseUrl(config('marketplaces.tiktok.base_url', 'https://open-api.tiktokglobalshop.com'))
            ->timeout(60)
            ->connectTimeout(10)
            ->acceptJson()
            ->withHeaders(['x-tts-access-token' => $this->integration->getAccessToken() ?? ''])
            ->attach('data', $contents, $fileName)
            ->post($apiPath.'?'.http_build_query($query));

        $payload = $response->json() ?? [];
        if ($response->failed() || ($payload['code'] ?? 0) !== 0) {
            $this->handleError($response);
        }

        return $payload['data'] ?? [];
    }
}
