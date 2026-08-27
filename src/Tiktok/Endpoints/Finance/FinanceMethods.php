<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\Endpoints\Finance;

use SistemAtc\Marketplaces\Tiktok\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Finance\Statement;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Finance\StatementTransaction;

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
}
