<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Endpoints;

use SistemAtc\Marketplaces\Amazon\Client;
use SistemAtc\Marketplaces\Amazon\DTO\Response\Finance\FinancialEventGroupsPage;
use SistemAtc\Marketplaces\Amazon\DTO\Response\Finance\FinancialEvents;
use SistemAtc\Marketplaces\Amazon\DTO\Response\Finance\FinancialEventsPage;
use SistemAtc\Marketplaces\Amazon\Support\FlattensCsvQuery;

/**
 * Endpoint Finances v0 da SP-API — eventos financeiros (taxas, comissoes,
 * fees FBA, estornos) de um pedido + NFe de envio FBA BR.
 *
 * Amazon manda fees com valor NEGATIVO (deducao do repasse).
 * Rate limit Finances: 0.5 req/s + burst 30.
 */
class Finances
{
    use FlattensCsvQuery;

    public function __construct(
        private readonly Client $client,
    ) {}

    /**
     * NFe de UM envio FBA BR (GET /fba/outbound/brazil/v0/shipments/{id}/invoice).
     * Retorna `payload.invoice` ou [] em 404 (NFe ainda nao emitida).
     * Rate limit FBA Invoice: 1.133 req/s + burst 25.
     *
     * @return array<string, mixed>
     */
    public function getFbaShipmentInvoice(string $shipmentId): array
    {
        $resp = $this->client->get(
            '/fba/outbound/brazil/v0/shipments/'.rawurlencode($shipmentId).'/invoice'
        );

        return data_get($resp, 'payload.invoice', []);
    }

    /**
     * Eventos financeiros de UM pedido → `payload.FinancialEvents` tipado
     * (FinancialEvents; vazio quando o settlement não postou).
     */
    public function listOrderFinancialEvents(string $amazonOrderId): FinancialEvents
    {
        $resp = $this->client->get(
            '/finances/v0/orders/'.rawurlencode($amazonOrderId).'/financialEvents'
        );

        return FinancialEvents::fromArray(data_get($resp, 'payload.FinancialEvents', []));
    }

    /**
     * Eventos financeiros de UM PERÍODO (GET /finances/v0/financialEvents) —
     * devolve os eventos de VÁRIOS pedidos postados no intervalo, paginados por
     * NextToken. É o caminho eficiente pro backfill histórico: ~centenas de
     * eventos por chamada em vez de 1 chamada por pedido.
     *
     * Query aceita: PostedAfter (ISO8601, obrigatório na 1ª página), PostedBefore,
     * MaxResultsPerPage (1–100, default 100), NextToken (nas páginas seguintes —
     * quando presente, a Amazon IGNORA os demais filtros).
     *
     * Rate limit: 0.5 req/s + burst 30 (mesmo teto do por-pedido, mas rende
     * MUITO mais por chamada). Percorra as páginas até `nextToken === null`.
     *
     * @param  array<string, mixed>  $query
     */
    public function listFinancialEvents(array $query): FinancialEventsPage
    {
        $resp = $this->client->get('/finances/v0/financialEvents', $query);

        return FinancialEventsPage::fromArray(data_get($resp, 'payload', []));
    }

    /**
     * Lista os SETTLEMENTS (repasses REAIS) da Amazon num período — o valor que
     * ela de fato depositou, com status de transferência bancária. É o caminho
     * pra conferir "a Amazon me pagou certo?" (vs o net por-pedido, que é
     * calculado). Paginado por NextToken.
     *
     * Query aceita: FinancialEventGroupStartedAfter (ISO8601, obrigatório na 1ª
     * página), FinancialEventGroupStartedBefore, MaxResultsPerPage (1–100),
     * NextToken.
     *
     * @param  array<string, mixed>  $query
     */
    public function listFinancialEventGroups(array $query): FinancialEventGroupsPage
    {
        $resp = $this->client->get('/finances/v0/financialEventGroups', $query);

        return FinancialEventGroupsPage::fromArray(data_get($resp, 'payload', []));
    }

    /**
     * Eventos financeiros de UM settlement (grupo) → detalha o repasse por pedido
     * dentro daquele depósito. Fecha a conta: soma dos nets dos pedidos ~=
     * originalTotal do grupo. Paginado por NextToken (reusa FinancialEventsPage).
     *
     * @param  array<string, mixed>  $query  (opcional: MaxResultsPerPage, PostedAfter/Before, NextToken)
     */
    public function listFinancialEventsByGroupId(string $eventGroupId, array $query = []): FinancialEventsPage
    {
        $resp = $this->client->get(
            '/finances/v0/financialEventGroups/'.rawurlencode($eventGroupId).'/financialEvents',
            $query
        );

        return FinancialEventsPage::fromArray(data_get($resp, 'payload', []));
    }

    // -------------------------------------------------------------------
    // Finances 2024-06-19 — transações (sucessora do financialEvents v0)
    // -------------------------------------------------------------------

    /**
     * Lista as TRANSAÇÕES financeiras (GET /finances/2024-06-19/transactions).
     * É a API nova que substitui o `financialEvents` v0: cada linha é uma
     * transação com `transactionType` (Shipment, Refund, Transfer/Payout,
     * ServiceFee, Adjustment…), `transactionStatus` (DEFERRED, RELEASED,
     * DEFERRED_RELEASED), `totalAmount`, `breakdowns` (Principal, Commission,
     * FBA fee, Tax…) e `relatedIdentifiers` (ORDER_ID, SHIPMENT_ID,
     * FINANCIAL_EVENT_GROUP_ID…). É a fonte pra montar o REPASSE por pedido
     * e reconciliar com o depósito (transfer) que a Amazon fez.
     *
     * Query: postedAfter (ISO8601; obrigatório na 1ª página; > 2 min antes da
     * requisição), postedBefore, marketplaceId, transactionStatus,
     * relatedIdentifierName (FINANCIAL_EVENT_GROUP_ID | ORDER_ID) +
     * relatedIdentifierValue, nextToken (com nextToken a Amazon ignora os
     * demais filtros). Eventos das últimas 48h podem não aparecer.
     *
     * Devolve o JSON inteiro: dados em `payload.transactions`, paginação em
     * `payload.nextToken`. Rate limit: 0.5 req/s + burst 10.
     *
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    public function listTransactions(array $query = []): array
    {
        return $this->client->get('/finances/2024-06-19/transactions', $this->csv($query));
    }

    /**
     * SALDOS da conta (GET /finances/2024-06-19/balances) — atual ou histórico
     * (asOfDate). Sub-saldos por tipo de conta e marketplace: AVAILABLE,
     * RESERVED, TOTAL, DEFERRED, ACCOUNT_LEVEL_RESERVE (balanceType).
     *
     * Query: marketplaceIds (array → csv), balanceType, accountType, asOfDate
     * (ISO8601 date), nextToken (> 500 resultados).
     * Retorna `balances[]` + `nextToken` no topo (sem `payload`).
     * Rate limit: 0.5 req/s + burst 10.
     *
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    public function listBalances(array $query = []): array
    {
        return $this->client->get('/finances/2024-06-19/balances', $this->csv($query));
    }

    /**
     * RESUMO financeiro de um período ou de um settlement
     * (GET /finances/2024-06-19/summary) — totais agregados (vendas, fees,
     * estornos, transferências) sem abrir linha a linha. Filtrando por
     * relatedIdentifierName=SETTLEMENT_ID + relatedIdentifierValue devolve o
     * fechamento daquele repasse (bate com `listFinancialEventGroups`).
     *
     * Query: marketplaceIds (array → csv), accountType, relatedIdentifierName
     * (só SETTLEMENT_ID), relatedIdentifierValue, periodStart, periodEnd
     * (ISO8601), nextToken. Retorna `summaries[]` + `nextToken`.
     * Rate limit: 0.5 req/s + burst 10.
     *
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    public function listSummary(array $query = []): array
    {
        return $this->client->get('/finances/2024-06-19/summary', $this->csv($query));
    }

    // -------------------------------------------------------------------
    // Transfers 2024-06-01 — payouts (depósitos) e métodos de pagamento
    // -------------------------------------------------------------------

    /**
     * Dispara um PAYOUT sob demanda (POST /finances/transfers/2024-06-01/payouts)
     * pro método de depósito padrão do Seller Central, se elegível. Só um
     * payout on-demand por marketplace/accountType por vez. Retorna
     * `payoutReferenceId`. Rate limit: 0.017 req/s + burst 2 (~1/min).
     *
     * @return array<string, mixed>
     */
    public function initiatePayout(string $marketplaceId, string $accountType): array
    {
        return $this->client->post('/finances/transfers/2024-06-01/payouts', [
            'marketplaceId' => $marketplaceId,
            'accountType' => $accountType,
        ]);
    }

    /**
     * Lista os PAYOUTS já feitos (GET /finances/transfers/2024-06-01/payouts):
     * cada depósito com id, valor, moeda, data, status e conta destino. É o
     * "extrato do banco" do lado da Amazon — casa com o financialEventGroup
     * (settlement) e com o crédito na conta corrente do ERP.
     *
     * Query: marketplaceIds (array → csv), createdAfter, createdBefore
     * (ISO8601), payoutId, accountType, nextToken. Retorna `payouts[]` +
     * `nextToken`. Rate limit: 0.5 req/s + burst 10.
     *
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    public function listPayouts(array $query = []): array
    {
        return $this->client->get('/finances/transfers/2024-06-01/payouts', $this->csv($query));
    }

    /**
     * Métodos de pagamento cadastrados (GET /finances/transfers/2024-06-01/paymentMethods)
     * pro marketplace: conta bancária, cartão, Seller Wallet.
     *
     * @param  list<string>  $paymentMethodTypes  BANK_ACCOUNT | CARD | SELLER_WALLET (vazio = todos)
     * @return array<string, mixed>  `paymentMethods[]`
     */
    public function getPaymentMethods(string $marketplaceId, array $paymentMethodTypes = []): array
    {
        $query = ['marketplaceId' => $marketplaceId];
        if ($paymentMethodTypes !== []) {
            $query['paymentMethodTypes'] = $paymentMethodTypes;
        }

        return $this->client->get('/finances/transfers/2024-06-01/paymentMethods', $this->csv($query));
    }

    /**
     * PAYOUTS PREVISTOS (GET /finances/transfers/2024-06-01/payouts/expected) —
     * os próximos depósitos que a Amazon vai fazer (valor estimado + data
     * prevista). Serve pra projeção de caixa do repasse.
     *
     * Query: marketplaceIds (array → csv), accountType, nextToken. Retorna
     * `expectedPayouts[]` + `nextToken`. Rate limit: 0.5 req/s + burst 10.
     *
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    public function listExpectedPayouts(array $query = []): array
    {
        return $this->client->get('/finances/transfers/2024-06-01/payouts/expected', $this->csv($query));
    }

    // -------------------------------------------------------------------
    // Finances Invoices 2026-06-25 — faturas que a Amazon emite pro seller
    // -------------------------------------------------------------------

    /**
     * Cabeçalhos das FATURAS que a Amazon emitiu pro seller (fees, serviços,
     * publicidade…) — GET /finances/invoices/2026-06-25/invoices. NÃO confundir
     * com as NF-e de venda (Invoices API / getFbaShipmentInvoice).
     *
     * Query: fromIssueDate + toIssueDate (janela máx. 90 dias; ambos ou
     * nenhum — default = últimos 90 dias), invoicesModifiedAfter (ISO8601),
     * nextToken (página de 100). Retorna `invoices[]`, `numOfRecords`,
     * `nextToken`.
     *
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    public function getInvoiceHeaders(string $marketplaceId, array $query = []): array
    {
        return $this->client->get(
            '/finances/invoices/2026-06-25/invoices',
            $this->csv(['marketplaceId' => $marketplaceId] + $query),
        );
    }

    /**
     * UMA fatura Amazon→seller com os itens
     * (GET /finances/invoices/2026-06-25/invoices/{invoiceIdentifier}).
     * Retorna `invoiceHeader`, `invoiceItems[]` e `nextTokenForLineItems`
     * (itens paginados — repita passando o token).
     *
     * @return array<string, mixed>
     */
    public function getInvoice(string $invoiceIdentifier, string $marketplaceId, ?string $nextTokenForLineItems = null): array
    {
        $query = ['marketplaceId' => $marketplaceId];
        if ($nextTokenForLineItems !== null) {
            $query['nextTokenForLineItems'] = $nextTokenForLineItems;
        }

        return $this->client->get(
            '/finances/invoices/2026-06-25/invoices/'.rawurlencode($invoiceIdentifier),
            $query,
        );
    }
}
