<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\Endpoints\Finance;

use SistemAtc\Marketplaces\Tiktok\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Finance\Payment;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Finance\Statement;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Finance\StatementTransaction;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Finance\UnsettledTransaction;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Finance\Withdrawal;

class FinanceMethods extends BaseMethods
{
    /**
     * Lista os STATEMENTS (settlements/repasses REAIS) do vendedor num período —
     * o valor efetivamente repassado, com status de pagamento. Equivalente ao
     * financialEventGroups da Amazon (nível payout), vs o statement POR PEDIDO.
     *
     * Filtros: statement_time_ge / statement_time_lt (unix), page_size (≤100),
     * page_token, sort_field, sort_order, payment_status.
     *
     * @param  array<string, mixed>  $filters
     * @return array{statements: list<Statement>, next_page_token: ?string, total_count: ?int}
     */
    public function getStatements(array $filters = [], string $version = '202309'): array
    {
        $response = $this->makeRequest(HttpMethod::GET, "/finance/{$version}/statements", $filters);

        return [
            'statements' => array_map(
                static fn (array $s): Statement => Statement::fromArray($s),
                $response['data']['statements'] ?? [],
            ),
            'next_page_token' => $response['data']['next_page_token'] ?? null,
            'total_count' => $response['data']['total_count'] ?? null,
        ];
    }

    /**
     * Todas as transações de UM statement (demonstrativo) — vendas E ajustes.
     *
     * Diferente do `getOrderStatementTransactions`, que só enxerga o que está
     * amarrado a um pedido. Os ajustes que o canal lança fora do pedido
     * (`LOGISTICS_REIMBURSEMENT` para pacote perdido, por exemplo) só aparecem
     * aqui: o endpoint por pedido devolve vazio para o id do ajuste, e o
     * pedido ressarcido aparece com settlement ZERO, porque o dinheiro veio
     * pelo ajuste.
     *
     * O vínculo vem pronto em `adjustmentOrderId` — o pedido que o ajuste
     * ressarce. Não é preciso inferir.
     *
     * Paginação por `page_token`; `sort_field` é OBRIGATÓRIO (a API responde
     * 36009004 sem ele).
     *
     * @param  array<string, mixed>  $filters
     * @return array{statement_transactions: list<StatementTransaction>, next_page_token: ?string, total_count: ?int}
     */
    public function getStatementTransactions(string $statementId, array $filters = [], string $version = '202309'): array
    {
        $filters['sort_field'] ??= 'order_create_time';

        $response = $this->makeRequest(
            HttpMethod::GET,
            "/finance/{$version}/statements/".rawurlencode($statementId).'/statement_transactions',
            $filters,
        );

        return [
            'statement_transactions' => array_map(
                static fn (array $t): StatementTransaction => StatementTransaction::fromArray($t),
                $response['data']['statement_transactions'] ?? [],
            ),
            'next_page_token' => $response['data']['next_page_token'] ?? null,
            'total_count' => $response['data']['total_count'] ?? null,
        ];
    }

    /**
     * Extrato financeiro do pedido — taxas + settlement.
     *
     * @return list<StatementTransaction>
     */
    public function getOrderStatementTransactions(string $orderId, string $version = '202309'): array
    {
        $response = $this->makeRequest(
            HttpMethod::GET,
            "/finance/{$version}/orders/" . rawurlencode($orderId) . "/statement_transactions"
        );

        return array_map(
            static fn (array $t): StatementTransaction => StatementTransaction::fromArray($t),
            $response['data']['statement_transactions'] ?? [],
        );
    }

    /**
     * Lista os PAGAMENTOS — a transferencia efetiva pra conta bancaria do
     * vendedor, com status, data de liquidacao e cambio.
     *
     * Fecha o ciclo do repasse: `getStatements` diz quanto o TikTok APUROU,
     * este diz quanto SAIU pro banco. Um pagamento pode agregar varios
     * statements, e o valor creditado difere do apurado quando ha conversao
     * de moeda — por isso o DTO carrega os tres montantes (ver Payment).
     *
     * `sort_field` e' OBRIGATORIO na API e so' aceita `create_time`; ja' vai
     * preenchido pra nao cair em 36009004.
     *
     * Filtros: create_time_ge / create_time_lt (unix), page_size (<=100),
     * page_token, sort_order.
     *
     * @param  array<string, mixed>  $filters
     * @return array{payments: list<Payment>, next_page_token: ?string}
     */
    public function getPayments(array $filters = [], string $version = '202605'): array
    {
        $filters['sort_field'] ??= 'create_time';

        $response = $this->makeRequest(HttpMethod::GET, "/finance/{$version}/payments", $filters);

        return [
            'payments' => array_map(
                static fn (array $p): Payment => Payment::fromArray($p),
                $response['data']['payments'] ?? [],
            ),
            // A doc deste endpoint NAO devolve total_count — so' o cursor.
            'next_page_token' => $response['data']['next_page_token'] ?? null,
        ];
    }

    /**
     * Lista as transacoes AINDA NAO LIQUIDADAS — o repasse que esta por vir.
     *
     * Complementa o statement: enquanto `getStatementTransactions` mostra o que
     * ja' foi apurado, este mostra o pedido entregue/em transito cujo dinheiro
     * o TikTok ainda nao pagou. E' o que permite provisionar o Contas a Receber
     * antes do settlement.
     *
     * TODO valor e' ESTIMATIVA e muda ate' a liquidacao (ver UnsettledTransaction).
     *
     * Os totais (`sum_est_*`) sao da CONSULTA INTEIRA, nao da pagina — nao
     * somar de novo ao paginar.
     *
     * `sort_field` e' OBRIGATORIO e so' aceita `order_create_time`.
     * Janela de busca: search_time_ge / search_time_lt (unix) — repare que o
     * nome do filtro NAO e' create_time, como nos outros endpoints de finance.
     *
     * @param  array<string, mixed>  $filters
     * @return array{transactions: list<UnsettledTransaction>, next_page_token: ?string, total_count: ?int, sum_est_settlement_amount: ?string, sum_est_revenue_amount: ?string, sum_est_adjustment_amount: ?string, sum_est_fee_amount: ?string}
     */
    public function getUnsettledTransactions(array $filters = [], string $version = '202507'): array
    {
        $filters['sort_field'] ??= 'order_create_time';

        $response = $this->makeRequest(HttpMethod::GET, "/finance/{$version}/orders/unsettled", $filters);

        $data = $response['data'] ?? [];

        return [
            'transactions' => array_map(
                static fn (array $t): UnsettledTransaction => UnsettledTransaction::fromArray($t),
                $data['transactions'] ?? [],
            ),
            'next_page_token' => $data['next_page_token'] ?? null,
            'total_count' => $data['total_count'] ?? null,
            // Dinheiro segue STRING tambem no agregado.
            'sum_est_settlement_amount' => $data['sum_est_settlement_amount'] ?? null,
            'sum_est_revenue_amount' => $data['sum_est_revenue_amount'] ?? null,
            'sum_est_adjustment_amount' => $data['sum_est_adjustment_amount'] ?? null,
            'sum_est_fee_amount' => $data['sum_est_fee_amount'] ?? null,
        ];
    }

    /**
     * Lista as movimentacoes do SALDO da loja (saque, liquidacao, transferencia
     * e estorno) — ver Withdrawal pro significado de cada `type`.
     *
     * `types` e' OBRIGATORIO na API. Sem ele a chamada falha, entao o default
     * pede os quatro tipos: pedir so' WITHDRAW esconde o SETTLE, que e' o
     * credito do repasse no saldo.
     *
     * Aceita `types` como array por conveniencia, mas manda na query
     * SEPARADO POR VIRGULA: `http_build_query` viraria `types[0]=...`,
     * enquanto o SignatureGenerator serializa array como JSON — a assinatura
     * nao bateria com a URL e a chamada morreria em erro de sign.
     *
     * Filtros: create_time_ge / create_time_lt (unix), page_size (<=100),
     * page_token.
     *
     * @param  array<string, mixed>  $filters
     * @return array{withdrawals: list<Withdrawal>, next_page_token: ?string, total_count: ?int}
     */
    public function getWithdrawals(array $filters = [], string $version = '202309'): array
    {
        $filters['types'] ??= ['WITHDRAW', 'SETTLE', 'TRANSFER', 'REVERSE'];

        if (is_array($filters['types'])) {
            $filters['types'] = implode(',', $filters['types']);
        }

        $response = $this->makeRequest(HttpMethod::GET, "/finance/{$version}/withdrawals", $filters);

        return [
            'withdrawals' => array_map(
                static fn (array $w): Withdrawal => Withdrawal::fromArray($w),
                $response['data']['withdrawals'] ?? [],
            ),
            'next_page_token' => $response['data']['next_page_token'] ?? null,
            'total_count' => $response['data']['total_count'] ?? null,
        ];
    }
}
