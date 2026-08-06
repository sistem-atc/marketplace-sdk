<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\ConectaLa\Endpoints\Financial;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\ConectaLa\Bases\BaseMethods;

/**
 * Financeiro da Conecta Lá. A API usa DOIS prefixos (herança): `/Financial/...`
 * (conciliação, service-invoice) e `/financeiro/...` (extrato, painel legal,
 * ajustes, liberações, recuperações, ciclos, antecipação). Muitos recursos têm
 * uma variante FISCAL (mesma forma, sufixo `/fiscal`).
 */
class FinancialMethods extends BaseMethods
{
    // ---------------- Conciliação ----------------

    /** Lotes de conciliação (GET /Financial/conciliationlote). */
    public function conciliationBatches(int $page = 1, int $perPage = 100, array $filters = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/Financial/conciliationlote', array_merge(['page' => $page, 'per_page' => $perPage], $filters));
    }

    /** Conciliação de um lote (GET /Financial/conciliation/{lote}). */
    public function conciliationByBatch(string $lote, int $page = 1, int $perPage = 100): array
    {
        return $this->makeRequest(HttpMethod::GET, "/Financial/conciliation/{$lote}", ['page' => $page, 'per_page' => $perPage]);
    }

    /** Conciliação por loja (GET /Financial/conciliationstore/{lote}). */
    public function conciliationByStore(string $lote, int $page = 1, int $perPage = 100): array
    {
        return $this->makeRequest(HttpMethod::GET, "/Financial/conciliationstore/{$lote}", ['page' => $page, 'per_page' => $perPage]);
    }

    /** Gera conciliação financeira (POST /Financial/conciliation). */
    public function generateConciliation(array $body): array
    {
        return $this->makeRequest(HttpMethod::POST, '/Financial/conciliation', body: $body);
    }

    /** Cadastra NF de serviço da conciliação (POST /Financial/service-invoice). */
    public function registerServiceInvoice(array $body): array
    {
        return $this->makeRequest(HttpMethod::POST, '/Financial/service-invoice', body: $body);
    }

    // ---------------- Extrato ----------------

    /** Extrato financeiro (GET /financeiro/extrato/extrato). */
    public function extract(array $filters = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/financeiro/extrato/extrato', $filters);
    }

    // ---------------- Painel Legal ----------------

    /** Itens do painel legal (GET /financeiro/legalpanel). */
    public function legalPanelItems(int $page = 1, int $perPage = 50, array $filters = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/financeiro/legalpanel', array_merge(['page' => $page, 'per_page' => $perPage], $filters));
    }

    /** Cria item no painel legal (POST /financeiro/legalpanel). */
    public function createLegalPanelItem(array $body): array
    {
        return $this->makeRequest(HttpMethod::POST, '/financeiro/legalpanel', body: $body);
    }

    /** Atualiza item do painel legal (PUT /financeiro/legalpanel). */
    public function updateLegalPanelItem(array $body): array
    {
        return $this->makeRequest(HttpMethod::PUT, '/financeiro/legalpanel', body: $body);
    }

    // ---------------- Ajustes financeiros (+ fiscal) ----------------

    /** Ajustes financeiros (GET /financeiro/financialadjustment). */
    public function adjustments(array $filters = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/financeiro/financialadjustment', $filters);
    }

    public function createAdjustment(array $body): array
    {
        return $this->makeRequest(HttpMethod::POST, '/financeiro/financialadjustment', body: $body);
    }

    public function updateAdjustment(array $body): array
    {
        return $this->makeRequest(HttpMethod::PUT, '/financeiro/financialadjustment', body: $body);
    }

    /** Ajustes FISCAIS (GET /financeiro/financialadjustment/fiscal). */
    public function fiscalAdjustments(array $filters = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/financeiro/financialadjustment/fiscal', $filters);
    }

    public function createFiscalAdjustment(array $body): array
    {
        return $this->makeRequest(HttpMethod::POST, '/financeiro/financialadjustment/fiscal', body: $body);
    }

    public function updateFiscalAdjustment(array $body): array
    {
        return $this->makeRequest(HttpMethod::PUT, '/financeiro/financialadjustment/fiscal', body: $body);
    }

    // ---------------- Liberação de pagamento (+ fiscal) ----------------

    /** Liberações de pagamento (GET /financeiro/releasepayment). */
    public function releasePayments(array $filters = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/financeiro/releasepayment', $filters);
    }

    /** Detalhe da liberação (GET /financeiro/releasepayment/{id}). */
    public function releasePayment(string $id): array
    {
        return $this->makeRequest(HttpMethod::GET, "/financeiro/releasepayment/{$id}");
    }

    public function createReleasePayment(array $body): array
    {
        return $this->makeRequest(HttpMethod::POST, '/financeiro/releasepayment', body: $body);
    }

    public function updateReleasePayment(array $body): array
    {
        return $this->makeRequest(HttpMethod::PUT, '/financeiro/releasepayment', body: $body);
    }

    /** Detalhe da liberação FISCAL (GET /financeiro/releasepayment/fiscal/{id}). */
    public function fiscalReleasePayment(string $id): array
    {
        return $this->makeRequest(HttpMethod::GET, "/financeiro/releasepayment/fiscal/{$id}");
    }

    // ---------------- Recuperação de pagamento (+ fiscal) ----------------

    /** Recuperações (GET /financeiro/recoverypayment). */
    public function recoveryPayments(array $filters = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/financeiro/recoverypayment', $filters);
    }

    /** Edita data-gatilho da recuperação (PUT /financeiro/recoverypayment). */
    public function updateRecoveryPayment(array $body): array
    {
        return $this->makeRequest(HttpMethod::PUT, '/financeiro/recoverypayment', body: $body);
    }

    public function fiscalRecoveryPayments(array $filters = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/financeiro/recoverypayment/fiscal', $filters);
    }

    public function updateFiscalRecoveryPayment(array $body): array
    {
        return $this->makeRequest(HttpMethod::PUT, '/financeiro/recoverypayment/fiscal', body: $body);
    }

    // ---------------- Ciclos ----------------

    /** Ciclos de pagamento (GET /financeiro/paymentcycles). */
    public function paymentCycles(array $filters = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/financeiro/paymentcycles', $filters);
    }

    /** Ciclos FISCAIS (GET /financeiro/paymentcycles/fiscal). */
    public function fiscalPaymentCycles(array $filters = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/financeiro/paymentcycles/fiscal', $filters);
    }

    // ---------------- Antecipação ----------------

    /** Antecipações externas (GET /financeiro/anticipationexternal). */
    public function anticipations(array $filters = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/financeiro/anticipationexternal', $filters);
    }

    /** Edita antecipação (PUT /financeiro/anticipationexternal). */
    public function updateAnticipation(array $body): array
    {
        return $this->makeRequest(HttpMethod::PUT, '/financeiro/anticipationexternal', body: $body);
    }

    /** Lojas elegíveis à antecipação (GET /financeiro/antecipacao/stores). */
    public function anticipationStores(array $filters = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/financeiro/antecipacao/stores', $filters);
    }

    /** Pedidos elegíveis à antecipação (GET /financeiro/antecipacao/orders). */
    public function anticipationOrders(array $filters = []): array
    {
        return $this->makeRequest(HttpMethod::GET, '/financeiro/antecipacao/orders', $filters);
    }

    /** Antecipa pedidos (POST /financeiro/antecipacao/orders/anticipate). */
    public function anticipateOrders(array $body): array
    {
        return $this->makeRequest(HttpMethod::POST, '/financeiro/antecipacao/orders/anticipate', body: $body);
    }
}
