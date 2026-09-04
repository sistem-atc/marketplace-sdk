<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\Endpoints\Customer;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopify\Bases\BaseMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\Concerns\PaginatesRestByCursor;

/**
 * Clientes da loja.
 *
 * Recurso REST: `customer` (enderecos e buscas salvas ficam em
 * `customer_address` / `customer_saved_search`, fora deste lote).
 * Exige escopo `read_customers` / `write_customers`; PII sujeita a
 * aprovacao de acesso a dados protegidos em apps publicos.
 */
class CustomerMethods extends BaseMethods
{
    use PaginatesRestByCursor;

    /**
     * Lista clientes (1 pagina, max 250).
     *
     * @param  array<string, mixed>  $params  ids, since_id, created_at_min/max, updated_at_min/max, limit, fields
     */
    public function list(array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/customers', $params);
    }

    /**
     * Itera todos os clientes (paginacao por cursor).
     *
     * @param  array<string, mixed>  $params  filtros so' na 1a pagina
     * @return \Generator<int, array<string, mixed>>
     */
    public function each(array $params = [], int $limit = 250): \Generator
    {
        return $this->eachPage('/customers.json', 'customers', $params, $limit);
    }

    /**
     * Total de clientes.
     *
     * @param  array<string, mixed>  $params  created_at_min/max, updated_at_min/max
     */
    public function count(array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/customers/count', $params);
    }

    /**
     * Recupera um cliente.
     */
    public function get(int|string $customerId, array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, "/customers/{$customerId}", $params);
    }

    /**
     * Busca clientes pela sintaxe de query da Shopify (ex.: `email:joao@x.com`, `tag:vip`).
     *
     * @param  array<string, mixed>  $params  order, limit, fields
     */
    public function search(string $query, array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/customers/search', array_merge(['query' => $query], $params));
    }

    /**
     * Pedidos de um cliente.
     *
     * @param  array<string, mixed>  $params  status (open|closed|cancelled|any)
     */
    public function orders(int|string $customerId, array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, "/customers/{$customerId}/orders", $params);
    }

    /**
     * Cria um cliente. Embrulha em `customer`.
     *
     * @param  array<string, mixed>  $customer
     */
    public function create(array $customer): array
    {
        return $this->makeRequest(HttpMethod::POST, '/customers', [], ['customer' => $customer]);
    }

    /**
     * Atualiza um cliente. Embrulha em `customer`.
     *
     * @param  array<string, mixed>  $customer
     */
    public function update(int|string $customerId, array $customer): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/customers/{$customerId}", [], ['customer' => $customer]);
    }

    /**
     * Remove um cliente (falha se tiver pedidos).
     */
    public function delete(int|string $customerId): array
    {
        return $this->makeRequest(HttpMethod::DELETE, "/customers/{$customerId}");
    }

    /**
     * Gera a URL de ativacao de conta (cliente ainda nao ativado).
     */
    public function accountActivationUrl(int|string $customerId): array
    {
        return $this->makeRequest(HttpMethod::POST, "/customers/{$customerId}/account_activation_url");
    }

    /**
     * Envia o convite de ativacao de conta por e-mail. Embrulha em `customer_invite`.
     *
     * @param  array<string, mixed>  $invite  to, from, subject, custom_message, bcc
     */
    public function sendInvite(int|string $customerId, array $invite = []): array
    {
        return $this->makeRequest(HttpMethod::POST, "/customers/{$customerId}/send_invite", [], ['customer_invite' => $invite]);
    }
}
