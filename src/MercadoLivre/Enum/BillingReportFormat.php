<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\Enum;

/**
 * Formato do report assincrono ML — usado em POST
 * /billing/integration/periods/key/{key}/reports.
 *
 * Cada group suporta combinacoes diferentes:
 *   - ML/MP/FLEX/INSURTECH: CSV ou XLSX
 *   - FULL/PAYMENT: SOMENTE XLSX
 */
enum BillingReportFormat: string
{
    case CSV = 'CSV';
    case XLSX = 'XLSX';
}
