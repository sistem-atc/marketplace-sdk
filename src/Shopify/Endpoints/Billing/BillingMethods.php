<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\Endpoints\Billing;

use SistemAtc\Marketplaces\Shopify\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;

class BillingMethods extends BaseMethods
{
    /**
     * Recupera transacoes financeiras do pedido (pagamentos, taxas, reembolsos).
     */
    public function getTransactions(int|string $orderId): array
    {
        return $this->makeRequest(HttpMethod::GET, "/orders/{$orderId}/transactions");
    }

    /**
     * Anexa informações de faturamento (NF-e) ao pedido via Metafields.
     * Padrao comum para integracoes brasileiras no Shopify.
     */
    public function attachInvoiceMetafields(int|string $orderId, array $invoiceData): array
    {
        // Exemplo de invoiceData: ['chave' => '...', 'numero' => '...', 'serie' => '...']
        $results = [];
        foreach ($invoiceData as $key => $value) {
            $results[] = $this->makeRequest(HttpMethod::POST, "/orders/{$orderId}/metafields", [], [
                'metafield' => [
                    'namespace' => 'faturamento',
                    'key' => $key,
                    'value' => $value,
                    'type' => 'single_line_text_field',
                ],
            ]);
        }
        return $results;
    }

    /**
     * Adiciona uma nota ao pedido com informacoes da nota fiscal.
     */
    public function addInvoiceNote(int|string $orderId, string $note): array
    {
        $order = $this->makeRequest(HttpMethod::GET, "/orders/{$orderId}");
        $currentNote = $order['order']['note'] ?? '';
        $newNote = trim($currentNote . "\n" . $note);

        return $this->makeRequest(HttpMethod::PUT, "/orders/{$orderId}", [], [
            'order' => [
                'id' => $orderId,
                'note' => $newNote,
            ],
        ]);
    }
}
