<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\Endpoints\Returns;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\MercadoLivre\Bases\BaseMethods;

/**
 * Devoluções e trocas pós-venda (doc "Gerenciar devoluções" + "Trocas").
 * Tudo pende de uma reclamação (claim): a devolução nasce quando a
 * resolução esperada vira `return_product`; a troca (change) quando o
 * comprador aceita `allow-replace`.
 */
class ReturnMethods extends BaseMethods
{
    private const CLAIMS_V1 = '/post-purchase/v1/claims';

    private const CLAIMS_V2 = '/post-purchase/v2/claims';

    private const RETURNS_V1 = '/post-purchase/v1/returns';

    /**
     * Devolução da reclamação (GET /post-purchase/v2/claims/{id}/returns):
     * `{id, status, shipments[{shipment_id, status, tracking_number,
     * destination}], refund_at, status_money, ...}`. O `id` é o return_id
     * usado em reviews()/createReview().
     *
     * @return array<string, mixed>
     */
    public function get(int|string $claimId): array
    {
        return $this->makeRequest(HttpMethod::GET, self::CLAIMS_V2."/{$claimId}/returns");
    }

    /**
     * Revisões feitas na devolução (GET /post-purchase/v1/returns/{returnId}/reviews):
     * `{reviews[{resource, resource_id, method: triage|seller, resource_reviews[
     * {status, product_condition, product_destination, ...}]}]}`.
     *
     * @return array<string, mixed>
     */
    public function reviews(int|string $returnId): array
    {
        return $this->makeRequest(HttpMethod::GET, self::RETURNS_V1."/{$returnId}/reviews");
    }

    /**
     * Motivos pra revisão com falha (GET /post-purchase/v1/returns/reasons?flow&claim_id).
     * Hoje só `flow=seller_return_failed`. Cada motivo traz `apply`:
     * `package` (vale pra toda a devolução, ex. SRF7 "não chegou") ou
     * `order` (por pedido).
     *
     * @return array<int, array<string, mixed>>
     */
    public function reasons(int|string $claimId, string $flow = 'seller_return_failed'): array
    {
        return $this->makeRequest(HttpMethod::GET, self::RETURNS_V1.'/reasons', [
            'flow' => $flow,
            'claim_id' => $claimId,
        ]);
    }

    /**
     * Sobe anexo pra revisão da devolução
     * (POST /post-purchase/v1/claims/{id}/returns/attachments, multipart `file`).
     * Devolve `{user_id, file_name}` — o `file_name` vai em `attachments[]`
     * do createReview().
     *
     * @return array<string, mixed>
     */
    public function uploadAttachment(int|string $claimId, string $contents, string $filename): array
    {
        $response = (clone $this->httpClient)
            ->attach('file', $contents, $filename)
            ->post(self::CLAIMS_V1."/{$claimId}/returns/attachments");

        if ($response->failed()) {
            $this->handleError($response);
        }

        return $response->json() ?? [];
    }

    /**
     * Registra a revisão do seller sobre o produto devolvido
     * (POST /post-purchase/v1/returns/{returnId}/return-review).
     * Body VAZIO (`[]`) = produto chegou OK. Com falha: lista de
     * `{reason, message, attachments[]?, order_id?}` — `order_id` só quando o
     * motivo tem `apply=order` (carrinho com vários pedidos).
     *
     * @param  array<int, array<string, mixed>>  $failures
     * @return array<string, mixed>
     */
    public function createReview(int|string $returnId, array $failures = []): array
    {
        return $this->makeRequest(
            HttpMethod::POST,
            self::RETURNS_V1."/{$returnId}/return-review",
            [],
            array_values($failures),
        );
    }

    /**
     * Custo do envio de devolução/troca cobrado na reclamação
     * (GET /post-purchase/v1/claims/{id}/charges/return-cost):
     * `{currency_id, amount, amount_usd?}` — `amount_usd` só com
     * `calculate_amount_usd=true`.
     *
     * @return array<string, mixed>
     */
    public function returnCost(int|string $claimId, bool $calculateUsd = false): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            self::CLAIMS_V1."/{$claimId}/charges/return-cost",
            $calculateUsd ? ['calculate_amount_usd' => 'true'] : [],
        );
    }

    /**
     * Trocas da reclamação (GET /post-purchase/v1/claims/{id}/changes):
     * `{paging, data[{claim_id, resource, resource_id, items[], new_orders_ids,
     * status, type, ...}]}`. Identifique troca pelo `related_entities`
     * contendo `change` no claim.
     *
     * @return array<string, mixed>
     */
    public function changes(int|string $claimId): array
    {
        return $this->makeRequest(HttpMethod::GET, self::CLAIMS_V1."/{$claimId}/changes");
    }

    /**
     * Seller oferece TROCA do produto
     * (POST /post-purchase/v1/claims/{id}/expected-resolutions/allow-replace).
     * Só quando `allow_replace` aparece em available_actions do claim; se o
     * comprador aceitar, a troca aparece em changes().
     *
     * @return array<string, mixed>
     */
    public function allowReplace(int|string $claimId): array
    {
        return $this->makeRequest(HttpMethod::POST, self::CLAIMS_V1."/{$claimId}/expected-resolutions/allow-replace");
    }
}
