<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Miolo de `data` do topico 66 — Appeal Completed: o recurso contra a
 * reprovacao de uma TABELA DE MEDIDAS (size chart) foi julgado, aprovado ou
 * rejeitado.
 *
 * ⚠️ Esta e' a UNICA doc de webhook do lote que NAO traz `## Event example` —
 * so' a tabela de parametros. O fixture foi montado a partir da tabela
 * (incluindo os campos marcados must-return = false), sem inventar campo algum.
 *
 * `product_id` esta' declarado `int64` na doc; tipamos STRING pela regra de IDs
 * do SDK. O evento nao diz qual foi o recurso original nem tem timestamp
 * proprio — a hora e' o `timestamp` do envelope.
 */
final class AppealCompletedWebhook implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?AppealTask $appealTask = null,
        /** Doc declara int64; string aqui por precisao. */
        public readonly ?string $productId = null,
    ) {}
}
