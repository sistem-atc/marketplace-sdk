<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Miolo de `data` do topico 63 — Activity change: mudou o CONTEUDO da campanha
 * (criacao, edicao, desativacao, e sobretudo entra/sai de produto).
 *
 * Complementa o topico 39 (Activity status change), que so' fala de status. Um
 * produto entrar numa promocao NAO gera evento 39.
 *
 * ⚠️ Divergencia doc x exemplo em `change_type`: a tabela lista CREATE / UPDATE
 * / DEACTIVATE (maiusculas), o exemplo oficial manda "product_update"
 * (minusculas, valor fora da lista). Por isso o campo e' `?string` cru, sem
 * enum — normalizar aqui quebraria com o valor real que chega.
 *
 * As duas listas sao DELTAS do evento, nao o estado final da campanha: pra ter
 * o escopo completo depois da mudanca, releia a campanha.
 */
final class ActivityChangeWebhook implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $activityId = null,
        /** Epoch em SEGUNDOS da mudanca. */
        public readonly ?int $updateTime = null,
        /** Doc: CREATE | UPDATE | DEACTIVATE. Exemplo real: "product_update". */
        public readonly ?string $changeType = null,
        public readonly ?ActivityChangeProductUpdateList $productUpdateList = null,
        public readonly ?ActivityChangeProductRemoveList $productRemoveList = null,
    ) {}
}
