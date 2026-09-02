<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\Endpoints\Customers;

use Generator;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\MercadoPago\Bases\BaseMethods;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\Customers\CustomerCardResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\Customers\CustomerResponseDTO;
use SistemAtc\Marketplaces\MercadoPago\DTO\Response\Customers\CustomerSearchResponseDTO;

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
     */
    public function create(array $payload): CustomerResponseDTO
    {
        return CustomerResponseDTO::fromArray($this->makeRequest(HttpMethod::POST, '/v1/customers', body: $payload));
    }

    public function createByEmail(string $email): CustomerResponseDTO
    {
        return $this->create(['email' => $email]);
    }

    public function get(string $customerId): CustomerResponseDTO
    {
        return CustomerResponseDTO::fromArray($this->makeRequest(HttpMethod::GET, '/v1/customers/'.rawurlencode($customerId)));
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function update(string $customerId, array $payload): CustomerResponseDTO
    {
        return CustomerResponseDTO::fromArray($this->makeRequest(HttpMethod::PUT, '/v1/customers/'.rawurlencode($customerId), body: $payload));
    }

    public function delete(string $customerId): CustomerResponseDTO
    {
        return CustomerResponseDTO::fromArray($this->makeRequest(HttpMethod::DELETE, '/v1/customers/'.rawurlencode($customerId)));
    }

    /**
     * Filtros: email, id, identification.type/number, limit, offset.
     *
     * @param  array<string, mixed>  $filters
     */
    public function search(array $filters = []): CustomerSearchResponseDTO
    {
        return CustomerSearchResponseDTO::fromArray($this->makeRequest(HttpMethod::GET, '/v1/customers/search', $filters));
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return Generator<int, CustomerResponseDTO>
     */
    public function searchAll(array $filters = [], int $limit = 100): Generator
    {
        return $this->paginate('/v1/customers/search', $filters, $limit, map: CustomerResponseDTO::fromArray(...));
    }

    // ── Cards ────────────────────────────────────────────────────────────

    /**
     * Salva um cartao no customer a partir de um card token (CardTokensMethods).
     *
     * @param  array<string, mixed>  $payload  token (obrigatorio), payment_method_id, issuer_id...
     */
    public function createCard(string $customerId, array $payload): CustomerCardResponseDTO
    {
        return CustomerCardResponseDTO::fromArray($this->makeRequest(HttpMethod::POST, '/v1/customers/'.rawurlencode($customerId).'/cards', body: $payload));
    }

    /**
     * @return list<CustomerCardResponseDTO>
     */
    public function listCards(string $customerId): array
    {
        return $this->hydrateList($this->makeRequest(HttpMethod::GET, '/v1/customers/'.rawurlencode($customerId).'/cards'), CustomerCardResponseDTO::class);
    }

    public function getCard(string $customerId, string $cardId): CustomerCardResponseDTO
    {
        return CustomerCardResponseDTO::fromArray($this->makeRequest(HttpMethod::GET, '/v1/customers/'.rawurlencode($customerId).'/cards/'.rawurlencode($cardId)));
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function updateCard(string $customerId, string $cardId, array $payload): CustomerCardResponseDTO
    {
        return CustomerCardResponseDTO::fromArray($this->makeRequest(HttpMethod::PUT, '/v1/customers/'.rawurlencode($customerId).'/cards/'.rawurlencode($cardId), body: $payload));
    }

    public function deleteCard(string $customerId, string $cardId): CustomerCardResponseDTO
    {
        return CustomerCardResponseDTO::fromArray($this->makeRequest(HttpMethod::DELETE, '/v1/customers/'.rawurlencode($customerId).'/cards/'.rawurlencode($cardId)));
    }
}
