<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\Endpoints\Transaction;

use SistemAtc\Marketplaces\Shopify\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;

class TransactionMethods extends BaseMethods
{
    /**
     * Lista transacoes de um pedido.
     */
    public function list(int|string $orderId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/orders/{$orderId}/transactions");
    }

    /**
     * Conta transacoes de um pedido.
     */
    public function count(int|string $orderId): int
    {
        $response = $this->makeRequest(HttpMethod::GET, "/orders/{$orderId}/transactions/count");
        return $response['count'] ?? 0;
    }

    /**
     * Recupera uma transacao especifica.
     */
    public function get(int|string $orderId, int|string $transactionId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/orders/{$orderId}/transactions/{$transactionId}");
    }

    /**
     * Cria uma transacao no pedido (capture, sale, void, refund...).
     * Ex.: ['kind' => 'capture', 'amount' => '10.00', 'parent_id' => 123].
     *
     * @param  array<string, mixed>  $transaction
     */
    public function create(int|string $orderId, array $transaction): array
    {
        return $this->makeRequest(HttpMethod::POST, "/orders/{$orderId}/transactions", [], ['transaction' => $transaction]);
    }
}
