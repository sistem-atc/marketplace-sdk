<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\Enum;

/**
 * Formato dos reports Mercado Pago (Released Money / Account Money /
 * Settlement). MP aceita CSV ou XLSX — CSV eh padrao porque eh mais facil
 * de parsear linha-a-linha sem dependencia externa.
 */
enum ReportFormat: string
{
    case CSV = 'csv';
    case XLSX = 'xlsx';
}
