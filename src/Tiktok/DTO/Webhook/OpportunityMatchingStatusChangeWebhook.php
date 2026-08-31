<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Miolo de `data` do topico 25 — Opportunity matching status change.
 *
 * Avisa que o matching de oportunidades de um produto CANDIDATO mudou de
 * status. Exige o escopo "Product Opportunities" habilitado no Partner Center;
 * a doc marca o topico como liberado so' pra um conjunto restrito de devs — se
 * ele nunca chegar, e' permissao, nao bug.
 *
 * ⚠️ O identificador aqui e' `external_product_id` — o ID do produto na
 * plataforma EXTERNA (a nossa), nao o product_id do TikTok. Nao tente casar com
 * a tabela de produtos TikTok.
 *
 * `opportunity_ids` e' lista de string; fica lista crua porque sao IDs simples.
 */
final class OpportunityMatchingStatusChangeWebhook implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param list<string>|null $opportunityIds */
    public function __construct(
        /** ID do produto na plataforma EXTERNA, nao no TikTok. */
        public readonly ?string $externalProductId = null,
        /** PENDING | MATCHED | NOT_MATCHED */
        public readonly ?string $opportunityMatchingStatus = null,
        public readonly ?array $opportunityIds = null,
        /** Epoch em SEGUNDOS — ate' quando o sistema segue tentando casar (default: upload + 60d). */
        public readonly ?int $opportunityMatchingEndTime = null,
        /** Epoch em SEGUNDOS da ultima mudanca de status. */
        public readonly ?int $updateTime = null,
    ) {}
}
