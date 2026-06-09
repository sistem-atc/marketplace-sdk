<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Mirakl\Endpoints\Finance;

use SistemAtc\Marketplaces\Mirakl\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;

class FinanceMethods extends BaseMethods
{
    public function listInvoices(array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, 'invoices', $params);
    }

    public function getInvoice(string $invoiceId): array
    {
        return $this->makeRequest(HttpMethod::GET, "invoices/{$invoiceId}");
    }

    public function listTransactions(array $params = []): array
    {
        return $this->makeRequest(HttpMethod::GET, 'payments/transactions', $params);
    }
}
