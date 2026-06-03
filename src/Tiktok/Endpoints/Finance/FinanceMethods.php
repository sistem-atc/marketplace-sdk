<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\Endpoints\Finance;

use SistemAtc\Marketplaces\Tiktok\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;

class FinanceMethods extends BaseMethods
{
    public function getOrderStatementTransactions(string $orderId, string $version = '202309'): array
    {
        $response = $this->makeRequest(
            HttpMethod::GET,
            "/finance/{$version}/orders/" . rawurlencode($orderId) . "/statement_transactions"
        );
        return $response['data']['statement_transactions'] ?? [];
    }
}
