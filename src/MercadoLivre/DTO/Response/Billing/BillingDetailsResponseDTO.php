<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoLivre\DTO\Response\Billing;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * RAIZ de GET /billing/integration/periods/key/{key}/group/{ML|MP}/details —
 * linhas de cobrança do período, com paginação por cursor (`lastId`).
 *
 * Consumidor: `->results` (list<BillingCharge>) + `->lastId` pro próximo from_id.
 *
 * @property list<BillingCharge> $results
 */
final class BillingDetailsResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param list<BillingCharge> $results */
    public function __construct(
        #[ArrayOf(BillingCharge::class)]
        public readonly array $results = [],
        public readonly mixed $lastId = null,
        public readonly mixed $paging = null,
    ) {}
}
