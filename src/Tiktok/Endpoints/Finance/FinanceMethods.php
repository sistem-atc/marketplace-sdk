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
