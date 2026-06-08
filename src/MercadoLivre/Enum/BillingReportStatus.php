<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\Enum;

/**
 * Estados de criacao de report assincrono ML.
 *
 * Doc: https://developers.mercadolivre.com.br/pt_br/baixar-documento-legal
 *
 * Fluxo:
 *   POST /reports          -> PROCESSING
 *   GET  /reports/{id}/status -> PROCESSING / READY / ERROR
 *   GET  /reports/{id}     -> download (so se READY)
 */
enum BillingReportStatus: string
{
    /** Report sendo gerado. Continue polling. */
    case PROCESSING = 'PROCESSING';

    /** Report pronto pra download. */
    case READY = 'READY';

    /** Falha na geracao. Re-criar (POST de novo). */
    case ERROR = 'ERROR';

    public function isTerminal(): bool
    {
        return $this !== self::PROCESSING;
    }
}
