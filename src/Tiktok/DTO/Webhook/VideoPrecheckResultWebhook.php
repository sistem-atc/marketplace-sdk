<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Miolo de `data` do topico 55 — Video Precheck Result.
 *
 * E' o CALLBACK assincrono do endpoint Precheck Video Content: a chamada
 * devolve so' um `precheck_task_id` e o veredito chega aqui. Sem escutar este
 * topico nao existe outro jeito de saber se o video passou.
 *
 * Envelope traz `creator_open_id` (nao `shop_id`) — e' topico do lado criador.
 *
 * ⚠️ `issues` so' vem quando `result` = FAIL. `result` PASS chega com a lista
 * ausente, nao vazia — trate como null.
 *
 * Nao confundir com o topico 59, que e' o precheck de video SHOPPABLE e tem
 * shape completamente diferente (dois blocos de checagem, e a chave do item se
 * chama `suggestions` no plural).
 */
final class VideoPrecheckResultWebhook implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param list<VideoPrecheckIssue>|null $issues */
    public function __construct(
        public readonly ?string $precheckTaskId = null,
        /** PASS | FAIL */
        public readonly ?string $result = null,
        #[ArrayOf(VideoPrecheckIssue::class)]
        public readonly ?array $issues = null,
    ) {}
}
