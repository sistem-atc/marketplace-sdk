<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\Endpoints\PreApprovals;

use Generator;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\MercadoPago\Bases\BaseMethods;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\PreApprovals\PreApprovalResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\PreApprovals\PreApprovalSearchResponseDTO;

/**
 * PreApproval — assinaturas (cobranca recorrente). Uma preapproval e' a
 * assinatura de UM pagador; pode nascer solta ou vinculada a um plano
 * (PreApprovalPlansMethods). Cada cobranca recorrente gera um
 * `authorized_payment` (AuthorizedPaymentsMethods).
 *
 * Doc: https://www.mercadopago.com.br/developers/pt/reference/subscriptions/_preapproval/post
 */
class PreApprovalsMethods extends BaseMethods
{
    /**
     * @param  array<string, mixed>  $payload  payer_email, reason, auto_recurring, back_url, card_token_id, status, preapproval_plan_id...
     */
    public function create(array $payload): PreApprovalResponseDTO
    {
        return PreApprovalResponseDTO::fromArray($this->makeRequest(HttpMethod::POST, '/preapproval', body: $payload));
    }

    public function get(string $preapprovalId): PreApprovalResponseDTO
    {
        return PreApprovalResponseDTO::fromArray($this->makeRequest(HttpMethod::GET, '/preapproval/'.rawurlencode($preapprovalId)));
    }

    /**
     * Pausar/cancelar/reativar e' via `status` (paused|cancelled|authorized).
     *
     * @param  array<string, mixed>  $payload
     */
    public function update(string $preapprovalId, array $payload): PreApprovalResponseDTO
    {
        return PreApprovalResponseDTO::fromArray($this->makeRequest(HttpMethod::PUT, '/preapproval/'.rawurlencode($preapprovalId), body: $payload));
    }

    /**
     * Filtros: payer_id, payer_email, preapproval_plan_id, status, q, sort, limit, offset.
     *
     * @param  array<string, mixed>  $filters
     */
    public function search(array $filters = []): PreApprovalSearchResponseDTO
    {
        return PreApprovalSearchResponseDTO::fromArray($this->makeRequest(HttpMethod::GET, '/preapproval/search', $filters));
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return Generator<int, PreApprovalResponseDTO>
     */
    public function searchAll(array $filters = [], int $limit = 100): Generator
    {
        return $this->paginate('/preapproval/search', $filters, $limit, map: PreApprovalResponseDTO::fromArray(...));
    }
}
