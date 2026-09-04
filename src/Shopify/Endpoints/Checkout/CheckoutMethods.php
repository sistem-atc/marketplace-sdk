<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\Endpoints\Checkout;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopify\Bases\BaseMethods;
use SistemAtc\Marketplaces\Shopify\Endpoints\Concerns\PaginatesRestByCursor;

/**
 * Checkouts (Checkout API) + checkouts abandonados.
 *
 * Recursos REST: `abandoned_checkout`, `checkout`.
 * Obs.: a Checkout API REST (create/update/complete) e' legada; a Shopify
 * recomenda Storefront/Cart para lojas novas. Mantida por paridade.
 */
class CheckoutMethods extends BaseMethods
{
    use PaginatesRestByCursor;

    /**
     * Lista checkouts abandonados (`GET /checkouts.json`).
     *
     * @param  array<string, mixed>  $params  status, created_at_min/max, updated_at_min/max, since_id, limit
     */
    public function listAbandoned(array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/checkouts', $params);
    }

    /**
     * Itera todos os checkouts abandonados (paginacao por cursor).
     *
     * @param  array<string, mixed>  $params
     * @return \Generator<int, array<string, mixed>>
     */
    public function eachAbandoned(array $params = [], int $limit = 250): \Generator
    {
        return $this->eachPage('/checkouts.json', 'checkouts', $params, $limit);
    }

    /**
     * Recupera um checkout pelo token.
     */
    public function get(string $token): array
    {
        return $this->makeRequest(HttpMethod::GET, "/checkouts/{$token}");
    }

    /**
     * Cria um checkout. Embrulha em `checkout`.
     *
     * @param  array<string, mixed>  $checkout
     */
    public function create(array $checkout): array
    {
        return $this->makeRequest(HttpMethod::POST, '/checkouts', [], ['checkout' => $checkout]);
    }

    /**
     * Atualiza um checkout pelo token. Embrulha em `checkout`.
     *
     * @param  array<string, mixed>  $checkout
     */
    public function update(string $token, array $checkout): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/checkouts/{$token}", [], ['checkout' => $checkout]);
    }

    /**
     * Completa um checkout (pagamento gratuito ou ja' processado).
     */
    public function complete(string $token): array
    {
        return $this->makeRequest(HttpMethod::POST, "/checkouts/{$token}/complete");
    }

    /**
     * Lista as opcoes de frete de um checkout.
     */
    public function shippingRates(string $token): array
    {
        return $this->makeRequest(HttpMethod::GET, "/checkouts/{$token}/shipping_rates");
    }
}
