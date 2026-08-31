<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Conteudo de `data` do webhook TYPE 51 — Global replication status change.
 *
 * So' pra seller global. Dispara no FIM da replicacao de um produto pra outro
 * mercado, com sucesso OU falha — `replicatedProduct->result` decide. DRAFT
 * conta como "terminou": o produto existe, mas precisa de correcao antes de ir
 * pra auditoria.
 */
final class GlobalReplicationStatusChangeWebhook implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $sourceProductId = null,
        public readonly ?ReplicatedProduct $replicatedProduct = null,
    ) {}
}
