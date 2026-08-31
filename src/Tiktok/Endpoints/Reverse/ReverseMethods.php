<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\Endpoints\Reverse;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Tiktok\Bases\BaseMethods;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Reverse\AftersaleEligibilityResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Reverse\CalculateRefundResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Reverse\CancelOrderResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Reverse\DecisionEligibilityResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Reverse\RejectReasonsResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Reverse\ReviewAftersalesResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Reverse\ReviewDecisionResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Reverse\SearchAftersalesResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Reverse\SearchCancellationsResponseDTO;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Reverse\SearchRmaResponseDTO;

/**
 * Return & Refund (pos-venda) do TikTok Shop.
 *
 * O TikTok separa o pos-venda em DOIS fluxos que nao se conversam:
 *
 *   - CANCELAMENTO (`/cancellations/*`) — antes do envio. Ids proprios
 *     (`cancel_id`), busca propria, aprovacao/rejeicao propria.
 *   - DEVOLUCAO (`/aftersales/*`, `/rma/*`) — depois do envio. Ids proprios
 *     (`aftersales_request_id`, `return_id`, RMA id).
 *
 * Quem quiser "tudo que estornou dinheiro" precisa varrer os DOIS.
 *
 * As versoes de path (202309, 202405, 202601..202606) NAO sao uniformes: o
 * TikTok versiona por endpoint. Manter cada uma exatamente como a doc manda —
 * trocar 202602 por 202603 na esperanca de "atualizar" devolve 404.
 */
class ReverseMethods extends BaseMethods
{
    public function searchReturns(array $filters = [], int $pageSize = 20): array
    {
        return $this->makeRequest(HttpMethod::POST, '/api/reverse/v202309/returns/search', [], array_merge([
            'page_size' => $pageSize,
        ], $filters));
    }

    public function getReturnDetail(string $reverseOrderId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/api/reverse/v202309/returns/{$reverseOrderId}");
    }

    // ─────────────────────────────────────────────────────────────────────
    // Devolucao / reembolso
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Simula o reembolso ANTES de aprovar (`CalculateRefundResponseDTO`).
     *
     * O valor MUDA conforme o `$requestType` (CANCEL, REFUND,
     * RETURN_AND_REFUND) para o MESMO pedido — sao politicas de calculo
     * diferentes. Nao cacheie por pedido; cacheie por (pedido, tipo).
     *
     * `$reasonName` e' a CHAVE do motivo (vinda do Get Aftersale Eligibility),
     * nunca o texto traduzido.
     *
     * @param  list<string>  $orderLineItemIds
     * @param  list<array{sku_id: string, quantity: int}>  $skus
     * @param  list<array{order_line_item_id?: string, sub_order_line_item_id?: string}>  $orderLineList
     *         Substitui `$orderLineItemIds` quando ha' bundle (sub-linhas).
     */
    public function calculateRefund(
        string $orderId,
        string $requestType,
        string $reasonName,
        array $orderLineItemIds = [],
        array $skus = [],
        array $orderLineList = [],
        ?string $shipmentType = null,
        ?string $handoverMethod = null,
    ): CalculateRefundResponseDTO {
        $body = array_filter([
            'order_id' => $orderId,
            'request_type' => $requestType,
            'reason_name' => $reasonName,
            'shipment_type' => $shipmentType,
            'handover_method' => $handoverMethod,
            'order_line_item_ids' => $orderLineItemIds ?: null,
            'skus' => $skus ?: null,
            'order_line_list' => $orderLineList ?: null,
        ], fn ($v) => $v !== null);

        $response = $this->makeRequest(HttpMethod::POST, '/return_refund/202602/refunds/calculate', [], $body);

        return CalculateRefundResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Busca solicitacoes de pos-venda (`SearchAftersalesResponseDTO`).
     *
     * `$whitelistedDataFields` NAO e' opcional na pratica: sem
     * `LINE_ITEMS` / `SKU_RETURN_REQUESTS` / `RETURN_MERCHANDISE_AUTHORIZATIONS`
     * a resposta vem praticamente so' com id + status, sem valor de reembolso.
     *
     * Paginacao por token OPACO: repita passando o `next_page_token` anterior.
     *
     * @param  list<string>  $whitelistedDataFields
     * @param  array<string, mixed>  $filters  aftersales_request_ids, aftersales_request_statuses,
     *                                         return_types, main_order_ids, buyer_ids, time{...}
     * @param  array{sort_field?: string, sort_order?: string}  $sort
     */
    public function searchAftersalesRequests(
        array $filters = [],
        array $whitelistedDataFields = ['LINE_ITEMS', 'SKU_RETURN_REQUESTS', 'RETURN_MERCHANDISE_AUTHORIZATIONS'],
        array $sort = [],
        int $pageSize = 20,
        ?string $pageToken = null,
        ?string $locale = null,
    ): SearchAftersalesResponseDTO {
        $body = array_filter([
            'whitelisted_data_fields' => $whitelistedDataFields ?: null,
            'filters' => $filters ?: null,
            'sort' => $sort ?: null,
            'pagination' => array_filter([
                'page_size' => $pageSize,
                'page_token' => $pageToken,
            ], fn ($v) => $v !== null),
        ], fn ($v) => $v !== null);

        $response = $this->makeRequest(
            HttpMethod::POST,
            '/return_refund/202603/aftersales/search',
            $locale !== null ? ['locale' => $locale] : [],
            $body,
        );

        return SearchAftersalesResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Busca RMAs — os PACOTES fisicos de devolucao (`SearchRmaResponseDTO`).
     *
     * Nao confunda com o aftersales request: um pedido de devolucao pode gerar
     * varios RMAs, e o recebimento de mercadoria se controla por AQUI.
     *
     * @param  array<string, mixed>  $filters  rma_ids, package_ids, statuses,
     *                                         aftersales_request_ids, main_order_ids, time{...}
     * @param  list<string>  $whitelistedDataFields
     * @param  array{sort_field?: string, sort_order?: string}  $sort
     */
    public function searchReturnMerchandiseAuthorizations(
        array $filters = [],
        array $whitelistedDataFields = ['LINE_ITEMS', 'SKU_RETURN_REQUESTS'],
        array $sort = [],
        int $pageSize = 20,
        ?string $pageToken = null,
        ?string $locale = null,
    ): SearchRmaResponseDTO {
        $body = array_filter([
            'whitelisted_data_fields' => $whitelistedDataFields ?: null,
            'filters' => $filters ?: null,
            'sort' => $sort ?: null,
            'pagination' => array_filter([
                'page_size' => $pageSize,
                'page_token' => $pageToken,
            ], fn ($v) => $v !== null),
        ], fn ($v) => $v !== null);

        $response = $this->makeRequest(
            HttpMethod::POST,
            '/return_refund/202604/rma/search',
            $locale !== null ? ['locale' => $locale] : [],
            $body,
        );

        return SearchRmaResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Aplica decisoes de pos-venda em LOTE (`ReviewAftersalesResponseDTO`).
     *
     * Sucesso HTTP nao quer dizer que tudo passou: o que falhou volta em
     * `data.errors`, item a item, com `code: 0` no envelope.
     *
     * O `idempotency_key` de cada decisao e' o que impede reembolso em dobro
     * quando o job re-tenta — gere um valor ESTAVEL por decisao (nao um uuid
     * novo a cada tentativa), senao ele nao serve pra nada.
     *
     * @param  list<array<string, mixed>>  $decisions  itens de `aftersales_request_decisions`
     */
    public function reviewAftersales(array $decisions): ReviewAftersalesResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::POST, '/return_refund/202606/aftersales/review', [], [
            'aftersales_request_decisions' => $decisions,
        ]);

        return ReviewAftersalesResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Decisoes candidatas POR LINHA da devolucao (`ReviewDecisionResponseDTO`).
     *
     * Traz tambem a faixa permitida de reembolso parcial. Decida pelo booleano
     * `eligible`: `ineligible_code`/`ineligible_reason` vem preenchidos mesmo
     * quando a decisao E' elegivel.
     *
     * @param  list<string>  $checkDecisions
     */
    public function getReviewDecision(
        string $returnOrCancelId,
        array $checkDecisions = [],
        ?int $sellerId = null,
        ?string $locale = null,
    ): ReviewDecisionResponseDTO {
        $response = $this->makeRequest(HttpMethod::GET, '/return_refund/202606/review_decision', array_filter([
            'return_or_cancel_id' => $returnOrCancelId,
            // Arrays na query do TikTok vao separados por virgula: passar array
            // faria o gerador de assinatura serializar em JSON e o `sign` nao
            // bateria com o que o servidor recalcula a partir da URL.
            'check_decisions' => $checkDecisions ? implode(',', $checkDecisions) : null,
            'seller_id' => $sellerId,
            'locale' => $locale,
        ], fn ($v) => $v !== null));

        return ReviewDecisionResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Elegibilidade das decisoes de uma devolucao/cancelamento
     * (`DecisionEligibilityResponseDTO`).
     *
     * Consulte ANTES de aprovar/rejeitar: o comprador pode ter desistido, o
     * prazo pode ter estourado, ou a arbitragem do TikTok pode ja' ter
     * decidido. Chamar a acao direto so' queima tentativa.
     *
     * @param  list<string>  $checkDecisions
     */
    public function getDecisionEligibility(
        string $returnOrCancelId,
        array $checkDecisions = [],
        ?string $locale = null,
    ): DecisionEligibilityResponseDTO {
        $response = $this->makeRequest(HttpMethod::GET, '/return_refund/202601/decision_eligibility', array_filter([
            'return_or_cancel_id' => $returnOrCancelId,
            'check_decisions' => $checkDecisions ? implode(',', $checkDecisions) : null,
            'locale' => $locale,
        ], fn ($v) => $v !== null));

        return DecisionEligibilityResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Motivos de rejeicao disponiveis (`RejectReasonsResponseDTO`).
     *
     * A lista e' POR devolucao/cancelamento, nao um catalogo fixo da loja:
     * cachear global entrega motivo invalido pro caso concreto.
     */
    public function getRejectReasons(string $returnOrCancelId, ?string $locale = null): RejectReasonsResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, '/return_refund/202309/reject_reasons', array_filter([
            'return_or_cancel_id' => $returnOrCancelId,
            'locale' => $locale,
        ], fn ($v) => $v !== null));

        return RejectReasonsResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Envia a etiqueta/QR code de devolucao e o rastreio pro comprador.
     *
     * UNICO endpoint MULTIPART do grupo — e' por isso que ele nao passa pelo
     * `makeRequest`: com `content-type: multipart/form-data` o corpo NAO entra
     * na assinatura (por isso `buildSignedQuery(..., null)`), enquanto o
     * `makeRequest` sempre assina o JSON do corpo.
     *
     * A doc lista o metodo como GET, mas o exemplo curl (e o servidor) usam
     * POST.
     *
     * @param  list<string>  $returnIds
     * @param  string  $returnIdType  RMA | RETURN — diz o que ha' em `$returnIds`
     * @param  array<string, string>  $files  campo => conteudo binario
     *                                        (`return_shipping_label`, `return_qr_code`)
     */
    public function uploadReturnShippingDocument(
        array $returnIds,
        string $returnIdType,
        ?string $trackingNumber = null,
        ?string $returnProviderId = null,
        array $files = [],
    ): array {
        $apiPath = '/return_refund/202405/returns/shipping_documents';

        $query = $this->buildSignedQuery($apiPath, [
            'return_ids' => implode(',', $returnIds),
            'return_id_type' => $returnIdType,
        ], null);

        $client = $this->httpClient;
        foreach ($files as $field => $contents) {
            $client = $client->attach($field, $contents, $field);
        }
        if ($files === []) {
            $client = $client->asMultipart();
        }

        $response = $client->post($apiPath.'?'.http_build_query($query), array_filter([
            'tracking_number' => $trackingNumber,
            'return_provider_id' => $returnProviderId,
        ], fn ($v) => $v !== null));

        $data = $response->json() ?? [];
        if ($response->failed() || ($data['code'] ?? 0) !== 0) {
            $this->handleError($response);
        }

        return $data;
    }

    // ─────────────────────────────────────────────────────────────────────
    // Cancelamento
    // ─────────────────────────────────────────────────────────────────────

    /**
     * Pede o cancelamento do pedido (`CancelOrderResponseDTO`).
     *
     * `cancel_status = CANCELLATION_REQUEST_SUCCESS` confirma o PEDIDO de
     * cancelamento, nao o cancelamento. O estado final chega pelo webhook
     * (type 11) ou pelo `searchCancellations`.
     *
     * @param  list<array{sku_id: string, quantity: int}>  $skus
     * @param  list<string>  $orderLineItemIds
     * @param  list<array{order_line_item_id?: string, sub_order_line_item_id?: string}>  $orderLineList
     */
    public function cancelOrder(
        string $orderId,
        string $cancelReason,
        array $skus = [],
        array $orderLineItemIds = [],
        array $orderLineList = [],
    ): CancelOrderResponseDTO {
        $body = array_filter([
            'order_id' => $orderId,
            'cancel_reason' => $cancelReason,
            'skus' => $skus ?: null,
            'order_line_item_ids' => $orderLineItemIds ?: null,
            'order_line_list' => $orderLineList ?: null,
        ], fn ($v) => $v !== null);

        $response = $this->makeRequest(HttpMethod::POST, '/return_refund/202602/cancellations', [], $body);

        return CancelOrderResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Busca cancelamentos (`SearchCancellationsResponseDTO`).
     *
     * ATENCAO: aqui a paginacao/ordenacao vai na QUERY e os filtros no BODY —
     * o inverso do `searchAftersalesRequests`, que poe tudo no corpo.
     *
     * @param  array<string, mixed>  $filters  cancel_ids, order_ids, buyer_user_ids, cancel_types,
     *                                         cancel_status, create_time_ge/lt, update_time_ge/lt, locale
     */
    public function searchCancellations(
        array $filters = [],
        int $pageSize = 10,
        ?string $pageToken = null,
        ?string $sortField = null,
        ?string $sortOrder = null,
    ): SearchCancellationsResponseDTO {
        $response = $this->makeRequest(
            HttpMethod::POST,
            '/return_refund/202602/cancellations/search',
            array_filter([
                'page_size' => (string) $pageSize,
                'page_token' => $pageToken,
                'sort_field' => $sortField,
                'sort_order' => $sortOrder,
            ], fn ($v) => $v !== null),
            $filters,
        );

        return SearchCancellationsResponseDTO::fromArray($response['data'] ?? []);
    }

    /**
     * Aprova o cancelamento pedido pelo comprador. `data` volta VAZIO — o
     * unico retorno util e' nao ter estourado excecao.
     *
     * O `$idempotencyKey` precisa ser ESTAVEL por cancelamento (nao um uuid
     * novo a cada retry), senao nao protege de aprovacao em duplicidade.
     */
    public function approveCancellation(string $cancelId, ?string $idempotencyKey = null): array
    {
        return $this->makeRequest(
            HttpMethod::POST,
            "/return_refund/202309/cancellations/{$cancelId}/approve",
            $idempotencyKey !== null ? ['idempotency_key' => $idempotencyKey] : [],
            // Sem corpo de propósito: a acao nao carrega dado nenhum e assim o
            // corpo fica FORA da assinatura. A doc mostra `-d '{}'`; se algum
            // dia voltar erro de `sign`, e' aqui que se mexe.
            null,
        );
    }

    /**
     * Rejeita o cancelamento pedido pelo comprador. `data` volta VAZIO.
     *
     * `$rejectReason` e' a CHAVE vinda do `getRejectReasons` (que e' por
     * cancelamento), nunca o texto traduzido.
     *
     * @param  list<array{image_id?: string, mime_type?: string, height?: int, width?: int}>  $images
     *         Evidencia de que o pedido ja' foi despachado, por exemplo.
     */
    public function rejectCancellation(
        string $cancelId,
        string $rejectReason,
        ?string $comment = null,
        array $images = [],
        ?string $idempotencyKey = null,
    ): array {
        $body = array_filter([
            'reject_reason' => $rejectReason,
            'comment' => $comment,
            'images' => $images ?: null,
        ], fn ($v) => $v !== null);

        return $this->makeRequest(
            HttpMethod::POST,
            "/return_refund/202309/cancellations/{$cancelId}/reject",
            $idempotencyKey !== null ? ['idempotency_key' => $idempotencyKey] : [],
            $body,
        );
    }

    /**
     * Elegibilidade de pos-venda do pedido (`AftersaleEligibilityResponseDTO`).
     *
     * Consulta que se faz ANTES de abrir cancelamento/devolucao: devolve, por
     * SKU e por tipo de pedido (CANCEL / RETURN / RETURN_AND_REFUND), se cabe
     * ou nao — e, quando nao cabe, o codigo e o motivo.
     *
     * O `$initiateAftersaleUser` MUDA o resultado para o MESMO pedido: o que o
     * comprador pode pedir nao e' o que o vendedor pode. A doc assume SELLER
     * quando omitido.
     *
     * `$requestTypes` vai como lista SEPARADA POR VIRGULA e nao como
     * `request_types[]=`: o curl oficial manda o valor cru na query, e o
     * http_build_query com array geraria chave indexada que quebra a
     * assinatura.
     *
     * @param  list<string>  $requestTypes  CANCEL | REFUND | RETURN_AND_REFUND
     */
    public function getAftersaleEligibility(
        string $orderId,
        ?string $initiateAftersaleUser = null,
        array $requestTypes = [],
    ): AftersaleEligibilityResponseDTO {
        $query = array_filter([
            'initiate_aftersale_user' => $initiateAftersaleUser,
            'request_types' => $requestTypes !== [] ? implode(',', $requestTypes) : null,
        ], static fn ($v) => $v !== null);

        $response = $this->makeRequest(
            HttpMethod::GET,
            "/return_refund/202602/orders/{$orderId}/aftersale_eligibility",
            $query,
        );

        return AftersaleEligibilityResponseDTO::fromArray($response['data'] ?? []);
    }
}
