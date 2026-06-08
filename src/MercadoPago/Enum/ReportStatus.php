<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\Enum;

/**
 * Estados de criacao dos reports Mercado Pago.
 *
 * Fluxo assincrono (igual reports ML billing):
 *   POST /v1/account/settlement_report      -> CREATED (pending)
 *   GET  /v1/account/settlement_report/list -> PROCESSING / READY / ERROR
 *   GET  /v1/account/settlement_report/{f}  -> download (so se READY)
 */
enum ReportStatus: string
{
    /** Job de geracao criado, ainda nao processou. */
    case PENDING = 'pending';

    /** Report sendo gerado. Continue polling. */
    case PROCESSING = 'processing';

    /** Report pronto pra download. */
    case READY = 'ready';

    /** Falha na geracao. Re-criar via novo POST. */
    case ERROR = 'error';

    /** Expirado / cancelado. */
    case EXPIRED = 'expired';

    public function isTerminal(): bool
    {
        return match ($this) {
            self::READY, self::ERROR, self::EXPIRED => true,
            self::PENDING, self::PROCESSING => false,
        };
    }

    public function isReady(): bool
    {
        return $this === self::READY;
    }
}
