<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de `POST /product/202604/opportunities/{opportunity_id}/submit`.
 *
 * O produto submetido precisa estar em ACTIVATE. Volta em PENDING_REVIEW.
 */
final class OpportunitySubmitResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?OpportunitySubmission $submission = null,
    ) {}
}
