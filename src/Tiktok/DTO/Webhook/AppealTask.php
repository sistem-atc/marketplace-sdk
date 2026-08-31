<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data.appeal_task` do topico 66 — a tarefa de recurso da tabela de medidas.
 *
 * `size_chart_id` e `size_chart_image_uri` sao opcionais (must-return = false):
 * o recurso pode ter sido aberto sobre a imagem sem a tabela cadastrada, ou
 * vice-versa.
 *
 * ⚠️ `size_chart_id` esta' declarado `int64` na doc; tipamos STRING seguindo a
 * regra do SDK pra IDs (int64 do TikTok e' snowflake e float/int de JS perde
 * precisao). O AutoHydrate converte int->string, entao o payload real funciona
 * dos dois jeitos.
 */
final class AppealTask implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param list<AppealAuditResult>|null $appealAuditResult */
    public function __construct(
        /** Doc declara int64; string aqui por precisao. Opcional. */
        public readonly ?string $sizeChartId = null,
        /** URI da imagem da tabela de medidas. Opcional. */
        public readonly ?string $sizeChartImageUri = null,
        /** Veredito por indicador. Sempre presente. */
        #[ArrayOf(AppealAuditResult::class)]
        public readonly ?array $appealAuditResult = null,
    ) {}
}
