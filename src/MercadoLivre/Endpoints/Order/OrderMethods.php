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

    /**
     * Descontos aplicados na venda (GET /orders/{id}/discounts): campanhas,
     * cupons e cashback, com `details[].items[].amounts` por unidade. Uma venda
     * pode ter mais de um desconto. ML guarda orders de ate 12 meses; como
     * vendedor, orders canceladas sao filtradas.
     *
     * @return array<string, mixed>
     */
    public function discounts(int|string $orderId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/orders/{$orderId}/discounts");
    }

    /**
     * Atributos dos produtos da venda (GET /orders/{id}/product) — ex.: IMEI e
     * entry_date. Para pedidos Fulfillment o IMEI tambem sai na nota fiscal.
     *
     * @return array{attributes?: list<array<string, mixed>>}
     */
    public function product(int|string $orderId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/orders/{$orderId}/product");
    }

    /**
     * Envios associados a venda (GET /orders/{id}/shipments). Manda sempre o
     * header `X-New-Domain: true` (roteia pra vista hosted, exigido em chamadas
     * publicas).
     *
     * GOTCHA de formato: sem `list_all` a vista atual devolve UM objeto {} (so o
     * forward); com `$listAll=true` devolve um ARRAY [] com forward + return.
     * A Hosted View (`$hosted=true`) SEMPRE devolve array. A vista sem hosted
     * sera descontinuada no fim de setembro/2026 — prefira ler como lista.
     * `$apiVersion='2'` (header X-Api-Version) traz PII completo em
     * receiver_address (receiver_name/receiver_phone).
     *
     * @return array<int|string, mixed>
     */
    public function shipments(
        int|string $orderId,
        bool $listAll = false,
        bool $hosted = false,
        ?string $apiVersion = null,
    ): array {
        $query = [];
        if ($listAll) $query['list_all'] = 'true';
        if ($hosted) $query['hosted'] = 'true';

        $headers = ['X-New-Domain' => 'true'];
        if ($apiVersion !== null) $headers['X-Api-Version'] = $apiVersion;

        return $this->makeRequest(HttpMethod::GET, "/orders/{$orderId}/shipments", $query, headers: $headers);
    }

    /**
     * Notas internas do vendedor na order (GET /orders/{id}/notes). Sao
     * anotacoes privadas (ate 300 chars), nao vao pro comprador.
     *
     * @return array<int|string, mixed>
     */
    public function notes(int|string $orderId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/orders/{$orderId}/notes");
    }

    /**
     * Cria nota interna na order (POST /orders/{id}/notes). Max 300 caracteres.
     *
     * @return array<string, mixed>
     */
    public function createNote(int|string $orderId, string $note): array
    {
        return $this->makeRequest(HttpMethod::POST, "/orders/{$orderId}/notes", body: ['note' => $note]);
    }

    /**
     * Altera o texto de uma nota interna (PUT /orders/{id}/notes/{noteId}).
     *
     * @return array<string, mixed>
     */
    public function updateNote(int|string $orderId, int|string $noteId, string $note): array
    {
        $noteId = rawurlencode((string) $noteId);

        return $this->makeRequest(HttpMethod::PUT, "/orders/{$orderId}/notes/{$noteId}", body: ['note' => $note]);
    }

    /**
     * Remove uma nota interna (DELETE /orders/{id}/notes/{noteId}). Corpo da
     * resposta costuma vir vazio -> [].
     *
     * @return array<string, mixed>
     */
    public function deleteNote(int|string $orderId, int|string $noteId): array
    {
        $noteId = rawurlencode((string) $noteId);

        return $this->makeRequest(HttpMethod::DELETE, "/orders/{$orderId}/notes/{$noteId}");
    }

    /**
     * Par de feedbacks da venda (GET /orders/{id}/feedback): `sale` (o que o
     * vendedor deu) e `purchase` (o que o comprador deu), cada um com seu
     * `id` — use-o em replyFeedback()/updateFeedback(). Vendedor acessa ate 5
     * anos de historico.
     *
     * GOTCHA: em algumas contas este endpoint devolve 403 invalid_seller_buyer_id;
     * nesse caso pegue os ids em order.feedback.{seller,buyer}.id (via get())
     * e use feedbackById().
     *
     * @return array{sale?: array<string, mixed>, purchase?: array<string, mixed>}
     */
    public function feedback(int|string $orderId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/orders/{$orderId}/feedback");
    }

    /**
     * Publica o feedback do vendedor na venda (POST /orders/{id}/feedback).
     * Body: fulfilled (bool, obrigatorio), rating (positive|neutral|negative,
     * so com fulfilled=true), message (< 160 chars), reason (obrigatorio com
     * fulfilled=false — ex.: OUT_OF_STOCK, BUYER_REGRETS), restock_item (bool).
     * So pode ser criado UMA vez (segundo POST = 400); nao aceita
     * fulfilled=false em order expirada.
     *
     * @param  array<string, mixed>  $feedback
     * @return array<string, mixed>
     */
    public function createFeedback(int|string $orderId, array $feedback): array
    {
        return $this->makeRequest(HttpMethod::POST, "/orders/{$orderId}/feedback", body: $feedback);
    }

    /**
     * Altera um feedback ja publicado (PUT /feedback/{id}) — mesmos campos do
     * createFeedback().
     *
     * @param  array<string, mixed>  $feedback
     * @return array<string, mixed>
     */
    public function updateFeedback(int|string $feedbackId, array $feedback): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/feedback/{$feedbackId}", body: $feedback);
    }

    /**
     * Responde o feedback recebido da contraparte (POST /feedback/{id}/reply).
     *
     * @return array<string, mixed>
     */
    public function replyFeedback(int|string $feedbackId, string $reply): array
    {
        return $this->makeRequest(HttpMethod::POST, "/feedback/{$feedbackId}/reply", body: ['reply' => $reply]);
    }

    /**
     * Bloqueia um comprador de ofertar/comprar nos anuncios do vendedor
     * (POST /users/{sellerId}/order_blacklist, body {user_id}). O id do
     * vendedor vai no path e o do comprador bloqueado no body.
     *
     * @return array<string, mixed>
     */
    public function blacklistBuyer(int|string $sellerId, int|string $buyerId): array
    {
        return $this->makeRequest(
            HttpMethod::POST,
            "/users/{$sellerId}/order_blacklist",
            body: ['user_id' => is_numeric($buyerId) ? (int) $buyerId : $buyerId],
        );
    }

    /**
     * Taxa de conversao entre moedas (GET /currency_conversions/search?from&to)
     * — devolve `{ratio}`. Necessario quando taxes.currency_id difere de
     * items.currency_id na order.
     *
     * @return array{ratio?: float}
     */
    public function currencyConversion(string $from, string $to): array
    {
        return $this->makeRequest(HttpMethod::GET, '/currency_conversions/search', ['from' => $from, 'to' => $to]);
    }
}
