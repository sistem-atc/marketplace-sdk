<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;

/**
 * Solicitations API v1 — pedir avaliação de produto + feedback do seller ao
 * comprador (o botão "Solicitar avaliação" do Seller Central), 1× por pedido,
 * entre 5 e 30 dias após a entrega.
 */
class Solicitations
{
    public function __construct(
        private readonly Client $client,
    ) {}

    /**
     * Quais solicitações estão disponíveis pro pedido
     * (GET /solicitations/v1/orders/{amazonOrderId}?marketplaceIds=…). HAL:
     * `_links.actions[]` (+ `_embedded.actions`). Vazio = não pode pedir
     * (já pedido, fora da janela, pedido cancelado…). Rate limit: 1 req/s + burst 5.
     *
     * @param  list<string>  $marketplaceIds
     * @return array<string, mixed>
     */
    public function getSolicitationActionsForOrder(string $amazonOrderId, array $marketplaceIds): array
    {
        return $this->client->get(
            '/solicitations/v1/orders/'.rawurlencode($amazonOrderId),
            ['marketplaceIds' => implode(',', $marketplaceIds)],
        );
    }

    /**
     * Dispara a solicitação de avaliação + feedback
     * (POST …/solicitations/productReviewAndSellerFeedback?marketplaceIds=…).
     * Sem body; resposta 201 vazia (ou `errors[]`). Rate limit: 1 req/s + burst 5.
     *
     * @param  list<string>  $marketplaceIds
     * @return array<string, mixed>
     */
    public function createProductReviewAndSellerFeedbackSolicitation(string $amazonOrderId, array $marketplaceIds): array
    {
        return $this->client->post(
            '/solicitations/v1/orders/'.rawurlencode($amazonOrderId)
            .'/solicitations/productReviewAndSellerFeedback'
            .'?'.http_build_query(['marketplaceIds' => implode(',', $marketplaceIds)]),
        );
    }
}
