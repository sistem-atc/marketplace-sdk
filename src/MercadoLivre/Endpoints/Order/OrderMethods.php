<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\Endpoints\Order;

use SistemAtc\Marketplaces\MercadoLivre\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Order\OrderResponseDTO;
use SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Order\OrderSearchResponseDTO;

class OrderMethods extends BaseMethods
{
    /**
     * Busca paginada de pedidos (GET /orders/search) como DTO tipado.
     * `->results` traz OrderResponseDTO (mesma arvore do get()); `->paging?->total`
     * pro controle de paginacao.
     */
    public function search(
        int|string $sellerId,
        ?string $dateFrom = null,
        ?string $dateTo = null,
        string $sort = 'date_desc',
        int $limit = 50,
        int $offset = 0,
    ): OrderSearchResponseDTO {
        $query = [
            'seller' => $sellerId,
            'sort' => $sort,
            'limit' => min($limit, 50),
            'offset' => $offset,
        ];

        if ($dateFrom !== null) $query['order.date_created.from'] = $dateFrom;
        if ($dateTo !== null) $query['order.date_created.to'] = $dateTo;

        return OrderSearchResponseDTO::fromArray(
            $this->makeRequest(HttpMethod::GET, '/orders/search', $query)
        );
    }

    /**
     * Pedido completo (GET /orders/{id}) como DTO tipado — ponto UNICO de parse.
     * A arvore inteira e' tipada (buyer/seller/order_items[].item/payments[]/
     * shipping/taxes/context/...) e `toArray()` e' LOSSLESS (validado contra 800
     * pedidos reais), entao serve tanto pra acesso tipado quanto pra gravar o raw.
     *
     * Mantem a semantica de erro do makeRequest: 404/falha estoura
     * MercadoLivreRequestException (nao retorna null).
     */
    public function get(int|string $orderId): OrderResponseDTO
    {
        return OrderResponseDTO::fromArray(
            $this->makeRequest(HttpMethod::GET, "/orders/{$orderId}")
        );
    }

    /**
     * Detalhe de um feedback de venda por id (GET /feedback/{id}) — rating,
     * message, from/to, role (seller|buyer), item, order_id.
     *
     * GOTCHA: o sub-endpoint /orders/{id}/feedback da 403 `invalid_seller_buyer_id`;
     * pegue os ids em order.feedback.seller.id / .buyer.id (via get()) e chame
     * este metodo. Alem disso, este endpoint antigo exige o access_token na
     * QUERY (Bearer sozinho da 403 "access_token_error"; Bearer + query = 200) —
     * por isso o token vai tambem em $query.
     */
    public function feedbackById(int|string $feedbackId): array
    {
        return $this->makeRequest(
            HttpMethod::GET,
            "/feedback/{$feedbackId}",
            ['access_token' => (string) $this->integration->getAccessToken()],
        );
    }
}
