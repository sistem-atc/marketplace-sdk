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

    // ---------------------------------------------------------------
    // Recurring Application Charges (cobranca recorrente do app)
    // ---------------------------------------------------------------

    /**
     * Lista cobrancas recorrentes do app.
     *
     * @param  array<string, mixed>  $params  ex.: since_id, fields
     */
    public function listRecurringCharges(array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/recurring_application_charges', $params);
    }

    /**
     * Recupera uma cobranca recorrente.
     *
     * @param  array<string, mixed>  $params  ex.: fields
     */
    public function getRecurringCharge(int|string $chargeId, array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, "/recurring_application_charges/{$chargeId}", $params);
    }

    /**
     * Cria uma cobranca recorrente (o lojista aprova via confirmation_url).
     *
     * @param  array<string, mixed>  $charge  ex.: name, price, return_url, trial_days, test
     */
    public function createRecurringCharge(array $charge): array
    {
        return $this->makeRequest(HttpMethod::POST, '/recurring_application_charges', [], ['recurring_application_charge' => $charge]);
    }

    /**
     * Cancela (remove) uma cobranca recorrente.
     */
    public function deleteRecurringCharge(int|string $chargeId): array
    {
        return $this->makeRequest(HttpMethod::DELETE, "/recurring_application_charges/{$chargeId}");
    }

    /**
     * Altera o capped_amount de uma cobranca recorrente (o lojista precisa
     * aprovar via update_capped_amount_url). A Shopify espera o valor como
     * query `recurring_application_charge[capped_amount]`.
     */
    public function customizeRecurringCharge(int|string $chargeId, string|float|int $cappedAmount): array
    {
        return $this->makeRequest(HttpMethod::PUT, "/recurring_application_charges/{$chargeId}/customize", [
            'recurring_application_charge' => ['capped_amount' => (string) $cappedAmount],
        ]);
    }

    // ---------------------------------------------------------------
    // Usage Charges (cobranca por uso, filha da recorrente)
    // ---------------------------------------------------------------

    /**
     * Lista cobrancas por uso de uma cobranca recorrente.
     *
     * @param  array<string, mixed>  $params  ex.: fields
     */
    public function listUsageCharges(int|string $recurringChargeId, array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, "/recurring_application_charges/{$recurringChargeId}/usage_charges", $params);
    }

    /**
     * Recupera uma cobranca por uso.
     *
     * @param  array<string, mixed>  $params  ex.: fields
     */
    public function getUsageCharge(int|string $recurringChargeId, int|string $usageChargeId, array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, "/recurring_application_charges/{$recurringChargeId}/usage_charges/{$usageChargeId}", $params);
    }

    /**
     * Cria uma cobranca por uso. Ex.: ['description' => '...', 'price' => '1.00'].
     *
     * @param  array<string, mixed>  $usageCharge
     */
    public function createUsageCharge(int|string $recurringChargeId, array $usageCharge): array
    {
        return $this->makeRequest(HttpMethod::POST, "/recurring_application_charges/{$recurringChargeId}/usage_charges", [], ['usage_charge' => $usageCharge]);
    }
}
