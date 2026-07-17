<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\Endpoints\Finance;

use SistemAtc\Marketplaces\Tiktok\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;
use SistemAtc\Marketplaces\Tiktok\DTO\Response\Finance\StatementTransaction;

class FinanceMethods extends BaseMethods
{
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
