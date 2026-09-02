<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\Endpoints\Customers;

use Generator;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\MercadoPago\Bases\BaseMethods;

/**
 * Customers + Cards — clientes salvos e cartoes tokenizados (Checkout
 * Transparente com "salvar cartao"). Espelha `CustomerClient` e
 * `CustomerCardClient` do SDK oficial numa classe so'.
 *
 * O MP nao aceita dois customers com o mesmo e-mail na mesma conta: o
 * `create` devolve 400 e o caminho certo e' `search(['email' => ...])`.
 *
 * Doc: https://www.mercadopago.com.br/developers/pt/reference/customers/_customers/post
 */
class CustomersMethods extends BaseMethods
{
    /**
     * @param  array<string, mixed>  $payload  email (obrigatorio), first_name, last_name, phone, identification, address...
     * @return array<string, mixed>
     */
    public function create(array $payload): array
    {
        return $this->makeRequest(HttpMethod::POST, '/v1/customers', body: $payload);
    }

    /**
     * @return array<string, mixed>
     */
    public function createByEmail(string $email): array
    {
        return $this->create(['email' => $email]);
    }

    /**
     * @return array<string, mixed>
     */
    public function get(string $customerId): array
    {
        return $this->makeRequest(HttpMethod::GET, '/v1/customers/'.rawurlencode($customerId));
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function update(string $customerId, array $payload): array
    {
        return $this->makeRequest(HttpMethod::PUT, '/v1/customers/'.rawurlencode($customerId), body: $payload);
    }

    /**
     * @return array<string, mixed>
     */
    public function delete(string $customerId): array
    {
        return $this->makeRequest(HttpMethod::DELETE, '/v1/customers/'.rawurlencode($customerId));
    }

    /**
     * Filtros: email, id, identification.type/number, limit, offset.
     *
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function search(array $filters = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/v1/customers/search', $filters);
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return Generator<int, array<string, mixed>>
     */
    public function searchAll(array $filters = [], int $limit = 100): Generator
    {
        return $this->paginate('/v1/customers/search', $filters, $limit);
    }

    // ── Cards ────────────────────────────────────────────────────────────

    /**
     * Salva um cartao no customer a partir de um card token (CardTokensMethods).
     *
     * @param  array<string, mixed>  $payload  token (obrigatorio), payment_method_id, issuer_id...
     * @return array<string, mixed>
     */
    public function createCard(string $customerId, array $payload): array
    {
        return $this->makeRequest(HttpMethod::POST, '/v1/customers/'.rawurlencode($customerId).'/cards', body: $payload);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function listCards(string $customerId): array
    {
        return $this->makeRequest(HttpMethod::GET, '/v1/customers/'.rawurlencode($customerId).'/cards');
    }

    /**
     * @return array<string, mixed>
     */
    public function getCard(string $customerId, string $cardId): array
    {
        return $this->makeRequest(HttpMethod::GET, '/v1/customers/'.rawurlencode($customerId).'/cards/'.rawurlencode($cardId));
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function updateCard(string $customerId, string $cardId, array $payload): array
    {
        return $this->makeRequest(HttpMethod::PUT, '/v1/customers/'.rawurlencode($customerId).'/cards/'.rawurlencode($cardId), body: $payload);
    }

    /**
     * @return array<string, mixed>
     */
    public function deleteCard(string $customerId, string $cardId): array
    {
        return $this->makeRequest(HttpMethod::DELETE, '/v1/customers/'.rawurlencode($customerId).'/cards/'.rawurlencode($cardId));
    }
}
