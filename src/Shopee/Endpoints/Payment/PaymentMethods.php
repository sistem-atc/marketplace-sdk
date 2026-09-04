<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\Endpoints\Payment;

use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Shopee\Bases\BaseMethods;
use SistemAtc\Marketplaces\Shopee\DTO\Response\Payment\EscrowDetailResponseDTO;
use SistemAtc\Marketplaces\Shopee\DTO\Response\Payment\WalletTransaction;

/**
 * Módulo `/api/v2/payment` — escrow (repasse por pedido), carteira (payout
 * real), extratos de renda (income statement/report) e parcelamento.
 *
 * Três níveis de "dinheiro" que NÃO se confundem:
 *  - escrow por pedido (`get_escrow_detail`, `get_escrow_list`): quanto a Shopee
 *    reteve/liberou de CADA pedido (taxas, comissão, cupom, frete);
 *  - carteira (`get_wallet_transaction_list`): movimento efetivo da conta do
 *    vendedor — liberação de escrow, ajustes, saques, recarga de Ads;
 *  - payout (`get_payout_info` / `get_billing_transaction_info`): repasse
 *    bancário — SÓ para vendedor Cross Border (CB); vendedor local BR recebe
 *    `error_param`/vazio.
 *
 * Datas são unix timestamp (segundos), exceto `get_income_detail` (YYYY-MM-DD).
 */
class PaymentMethods extends BaseMethods
{
    /**
     * Taxas + repasse do pedido.
     *
     * ->orderIncome->escrowAmount = liquido que a Shopee paga;
     * ->buyerPaymentInfo->buyerTotalAmount = o que o comprador pagou.
     */
    public function getEscrowDetail(string $orderSn): EscrowDetailResponseDTO
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/payment/get_escrow_detail', ['order_sn' => $orderSn]);

        return EscrowDetailResponseDTO::fromArray($response['response'] ?? []);
    }

    /**
     * Escrow de VÁRIOS pedidos numa chamada (mesmo payload de `get_escrow_detail`,
     * um item por pedido). Limite 1..50 order_sn; a Shopee recomenda até 20.
     *
     * GET com lista separada por vírgula. A resposta vem como LISTA no
     * `response` (não objeto): cada item tem `escrow_detail{order_sn, order_income…}`.
     *
     * @param  list<string>  $orderSnList
     * @return list<array<string, mixed>>  itens crus: [['escrow_detail' => [...]], ...]
     */
    public function getEscrowDetailBatch(array $orderSnList): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/payment/get_escrow_detail_batch', [
            'order_sn_list' => implode(',', $orderSnList),
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Lista de pedidos LIBERADOS (escrow release) num intervalo de release_time —
     * é a fonte para saber "quais pedidos viraram dinheiro na semana X" antes de
     * detalhar cada um com `getEscrowDetail`/`getEscrowDetailBatch`.
     *
     * Paginação por página (page_no 1-based, page_size ≤ 100, default 40) e
     * flag `more`. Cada item: order_sn, payout_amount, escrow_release_time.
     *
     * @return array{escrow_list: list<array<string, mixed>>, more: bool}
     */
    public function getEscrowList(int $releaseTimeFrom, int $releaseTimeTo, int $pageSize = 40, int $pageNo = 1): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/payment/get_escrow_list', [
            'release_time_from' => $releaseTimeFrom,
            'release_time_to' => $releaseTimeTo,
            'page_size' => $pageSize,
            'page_no' => $pageNo,
        ]);
        $resp = $response['response'] ?? [];

        return [
            'escrow_list' => $resp['escrow_list'] ?? [],
            'more' => (bool) ($resp['more'] ?? false),
        ];
    }

    /**
     * Transações da CARTEIRA num período — o nível SETTLEMENT/payout: dinheiro
     * que de fato entrou/saiu da conta Shopee do vendedor (o repasse real), vs o
     * escrow por-pedido. Equivalente ao financialEventGroups da Amazon.
     *
     * Params: create_time_from / create_time_to (unix), page_no (1-based),
     * page_size (≤100), wallet_type, transaction_type, money_flow.
     *
     * @param  array<string, mixed>  $params
     * @return array{transactions: list<WalletTransaction>, more: bool}
     */
    public function getWalletTransactionList(array $params): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/payment/get_wallet_transaction_list', $params);
        $resp = $response['response'] ?? [];

        return [
            'transactions' => array_map(
                static fn (array $t): WalletTransaction => WalletTransaction::fromArray($t),
                $resp['transaction_list'] ?? [],
            ),
            'more' => (bool) ($resp['more'] ?? false),
        ];
    }

    /**
     * Payouts (repasse bancário) — SÓ vendedor Cross Border. Janela máxima de
     * **15 dias** entre payout_time_from/to; page_size ≤ 100, page_no 1-based.
     * Cada payout traz valor, moeda, FX rate e a lista de pedidos/ajustes que o
     * compõem.
     *
     * A Shopee está substituindo esta API por `getPayoutInfo` (cursor) —
     * prefira a nova em código novo.
     *
     * @return array{payout_list: list<array<string, mixed>>, more: bool}
     */
    public function getPayoutDetail(int $payoutTimeFrom, int $payoutTimeTo, int $pageSize = 100, int $pageNo = 1): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/payment/get_payout_detail', [
            'payout_time_from' => $payoutTimeFrom,
            'payout_time_to' => $payoutTimeTo,
            'page_size' => $pageSize,
            'page_no' => $pageNo,
        ]);
        $resp = $response['response'] ?? [];

        return [
            'payout_list' => $resp['payout_list'] ?? [],
            'more' => (bool) ($resp['more'] ?? false),
        ];
    }

    /**
     * Substituta de `getPayoutDetail` (CB only) com paginação por CURSOR:
     * primeira chamada `cursor=''`, depois repassar `next_cursor` enquanto
     * `more=true`. Janela máxima de **15 dias**; page_size ≤ 100.
     *
     * Devolve `encrypted_payout_id` por payout — é a chave para detalhar as
     * transações com `getBillingTransactionInfo`.
     *
     * @return array{payout_list: list<array<string, mixed>>, more: bool, next_cursor: string}
     */
    public function getPayoutInfo(int $payoutTimeFrom, int $payoutTimeTo, int $pageSize = 100, string $cursor = ''): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/payment/get_payout_info', [
            'payout_time_from' => $payoutTimeFrom,
            'payout_time_to' => $payoutTimeTo,
            'page_size' => $pageSize,
            'cursor' => $cursor,
        ]);
        $resp = $response['response'] ?? [];

        return [
            'payout_list' => $resp['payout_list'] ?? [],
            'more' => (bool) ($resp['more'] ?? false),
            'next_cursor' => (string) ($resp['next_cursor'] ?? ''),
        ];
    }

    /**
     * Transações de faturamento (CB only) — linha a linha do que compõe um
     * payout, tanto já liberado quanto A LIBERAR:
     *  - `billingTransactionInfoType` 1 = TO_RELEASE (ainda retido), 2 = RELEASED;
     *  - `encryptedPayoutIds` (de `getPayoutInfo`) restringe aos payouts dados —
     *    só faz sentido com tipo 2 (RELEASED).
     *
     * POST; paginação por cursor (`''` na primeira, depois `next_cursor`).
     * Cada transação: amount, currency, order_sn, cost_header, scenario,
     * level (ORDER/SHOP), billing_transaction_type (ORDER_INCOME/ADJUSTMENT…),
     * billing_transaction_status.
     *
     * @param  list<string>  $encryptedPayoutIds
     * @return array{transactions: list<array<string, mixed>>, more: bool, next_cursor: string}
     */
    public function getBillingTransactionInfo(
        int $billingTransactionInfoType,
        string $cursor = '',
        int $pageSize = 100,
        array $encryptedPayoutIds = [],
    ): array {
        $body = [
            'billing_transaction_info_type' => $billingTransactionInfoType,
            'cursor' => $cursor,
            'page_size' => $pageSize,
        ];
        if ($encryptedPayoutIds !== []) {
            $body['encrypted_payout_ids'] = array_values($encryptedPayoutIds);
        }

        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/payment/get_billing_transaction_info', [], $body);
        $resp = $response['response'] ?? [];

        return [
            'transactions' => $resp['transactions'] ?? [],
            'more' => (bool) ($resp['more'] ?? false),
            'next_cursor' => (string) ($resp['next_cursor'] ?? ''),
        ];
    }

    /**
     * Visão consolidada "Income Overview" do Seller Center: total por status de
     * renda + data do último payout. `incomeStatus` (opcional): local 1=Released,
     * 2=Pending; CB 0=To Release, 1=Released. Sem filtro devolve todos.
     *
     * @return array<string, mixed>  latest_payout_date, total_income[]
     */
    public function getIncomeOverview(?int $incomeStatus = null): array
    {
        $query = $incomeStatus === null ? [] : ['income_status' => $incomeStatus];
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/payment/get_income_overview', $query);

        return $response['response'] ?? [];
    }

    /**
     * "Income Details" do Seller Center — renda por PEDIDO, segmentada por status.
     *
     * ⚠️ `dateFrom`/`dateTo` são **YYYY-MM-DD** (não unix) e só filtram quando
     * `incomeStatus` = Released; nos demais status a API devolve tudo que está
     * naquele estágio, ignorando o período. Cursor: `''` na primeira chamada.
     *
     * Retorno cru: a Shopee devolve o payload fora de `response` em alguns
     * ambientes (`income_detail_list.list`), por isso entregamos o body inteiro.
     *
     * @return array<string, mixed>
     */
    public function getIncomeDetail(
        string $dateFrom,
        string $dateTo,
        int $incomeStatus,
        int $pageSize = 100,
        string $cursor = '',
    ): array {
        return $this->makeRequest(HttpMethod::GET, '/api/v2/payment/get_income_detail', [
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'income_status' => $incomeStatus,
            'page_size' => $pageSize,
            'cursor' => $cursor,
        ]);
    }

    /**
     * Dispara a geração do extrato de renda (income statement, o PDF/XLSX que o
     * Seller Center oferece). Devolve o `id` para polling em `getIncomeStatement`.
     *
     * Regras da Shopee: semanal (`statementType` 1) exige from=segunda e
     * to=domingo (hora local); mensal (2) exige dia 1 e último dia do mês.
     * Vendedor local é obrigado a informar o tipo; CB pode omitir.
     */
    public function generateIncomeStatement(int $releaseTimeFrom, int $releaseTimeTo, ?int $statementType = null): int
    {
        $query = [
            'release_time_from' => $releaseTimeFrom,
            'release_time_to' => $releaseTimeTo,
        ];
        if ($statementType !== null) {
            $query['statement_type'] = $statementType;
        }

        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/payment/generate_income_statement', $query);

        return (int) ($response['response']['id'] ?? 0);
    }

    /**
     * Status do extrato pedido em `generateIncomeStatement`; quando pronto traz
     * `file_link` (URL temporária). Campos: id, file_name, status, generated_time, file_link.
     *
     * @return array<string, mixed>
     */
    public function getIncomeStatement(int $incomeStatementId): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/payment/get_income_statement', [
            'income_statement_id' => $incomeStatementId,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Dispara a geração do income REPORT (planilha analítica, por pedido) para o
     * intervalo de release_time. Devolve o `id` para `getIncomeReport`.
     */
    public function generateIncomeReport(int $releaseTimeFrom, int $releaseTimeTo): int
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/payment/generate_income_report', [
            'release_time_from' => $releaseTimeFrom,
            'release_time_to' => $releaseTimeTo,
        ]);

        return (int) ($response['response']['id'] ?? 0);
    }

    /**
     * Status + `file_link` do income report gerado por `generateIncomeReport`.
     *
     * @return array<string, mixed>  id, file_name, status, generated_time, file_link
     */
    public function getIncomeReport(int $incomeReportId): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/payment/get_income_report', [
            'income_report_id' => $incomeReportId,
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Métodos de pagamento disponíveis POR REGIÃO — API pública (assinatura só
     * com partner_id, sem access_token/shop_id).
     *
     * @return list<array{payment_method: list<string>, region: string}>
     */
    public function getPaymentMethodList(): array
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/payment/get_payment_method_list', publicApi: true);

        return $response['response'] ?? [];
    }

    /**
     * Estado do parcelamento em nível de LOJA (1 = ligado, 0 = desligado).
     */
    public function getShopInstallmentStatus(): int
    {
        $response = $this->makeRequest(HttpMethod::GET, '/api/v2/payment/get_shop_installment_status');

        return (int) ($response['response']['installment_status'] ?? 0);
    }

    /**
     * Liga/desliga o parcelamento em nível de LOJA. Devolve o status aplicado.
     */
    public function setShopInstallmentStatus(int $installmentStatus): int
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/payment/set_shop_installment_status', [], [
            'installment_status' => $installmentStatus,
        ]);

        return (int) ($response['response']['installment_status'] ?? $installmentStatus);
    }

    /**
     * Parcelas configuradas por ITEM (só TH/TW; em outras regiões volta vazio ou
     * erro). Até 100 item_id por chamada. POST.
     *
     * @param  list<int>  $itemIdList
     * @return array<string, mixed>  item_installment_list[], item_plan_ahora_list[]
     */
    public function getItemInstallmentStatus(array $itemIdList): array
    {
        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/payment/get_item_installment_status', [], [
            'item_id_list' => array_values($itemIdList),
        ]);

        return $response['response'] ?? [];
    }

    /**
     * Define as parcelas por ITEM (só TH/TW). `tenureList` = [] desliga; TH aceita
     * [3,6,10], TW aceita [3,6,12,24]. `participatePlanAhora` só para AR local.
     *
     * @param  list<int>  $itemIdList  até 100
     * @param  list<int>  $tenureList
     * @return array<string, mixed>  item_installment_list[], item_plan_ahora_list[]
     */
    public function setItemInstallmentStatus(array $itemIdList, array $tenureList, ?bool $participatePlanAhora = null): array
    {
        $body = [
            'item_id_list' => array_values($itemIdList),
            'tenure_list' => array_values($tenureList),
        ];
        if ($participatePlanAhora !== null) {
            $body['participate_plan_ahora'] = $participatePlanAhora;
        }

        $response = $this->makeRequest(HttpMethod::POST, '/api/v2/payment/set_item_installment_status', [], $body);

        return $response['response'] ?? [];
    }
}
