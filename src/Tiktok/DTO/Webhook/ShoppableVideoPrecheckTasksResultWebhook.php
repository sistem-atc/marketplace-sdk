<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Miolo de `data` do topico 59 — Shoppable Video Precheck Tasks Result.
 *
 * Callback assincrono do precheck de video SHOPPABLE. Envelope traz
 * `creator_open_id`.
 *
 * ⚠️ A chave da tarefa e' `task_id` aqui (no topico 55 e' `precheck_task_id`) e
 * o exemplo oficial manda um id no formato "task_202402011200001234", que NAO e'
 * snowflake numerico como a tabela sugere. Por isso string, sempre.
 *
 * Dois vereditos INDEPENDENTES: violacao e qualidade. Aprovar publicacao so'
 * quando os dois estiverem SUCCESS.
 */
final class ShoppableVideoPrecheckTasksResultWebhook implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Id da tarefa de precheck. Nem sempre numerico — sempre string. */
        public readonly ?string $taskId = null,
        public readonly ?ShoppableVideoViolationCheckResult $violationCheckResult = null,
        public readonly ?ShoppableVideoQualityCheckResult $goodQualityCheckResult = null,
    ) {}
}
