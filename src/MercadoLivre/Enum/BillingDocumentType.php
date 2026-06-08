<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\Enum;

/**
 * Tipos de documento de billing — controla se a busca retorna faturas
 * (cobrancas) ou notas de credito (estornos/devolucoes).
 *
 * Quase todos endpoints /billing/* exigem este parametro.
 */
enum BillingDocumentType: string
{
    /** Fatura — cobranca emitida pelo ML/MP contra o seller. */
    case BILL = 'BILL';

    /** Nota de credito — estorno emitido pelo ML/MP pro seller
     *  (devolucoes, cancelamentos, bonificacoes). */
    case CREDIT_NOTE = 'CREDIT_NOTE';
}
