<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Reverse;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` do Get Review Decision (`/return_refund/202606/review_decision`).
 *
 * Versao por-linha do Decision Eligibility. Guarde `requestLogId`: e' o que o
 * suporte do TikTok pede pra investigar decisao recusada.
 *
 * @property list<ReviewDecisionLineItem>|null $lineItems
 * @property list<ReviewError>|null $errors
 */
final class ReviewDecisionResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(ReviewDecisionLineItem::class)]
        public readonly ?array $lineItems = null,
        public readonly ?string $requestLogId = null,
        #[ArrayOf(ReviewError::class)]
        public readonly ?array $errors = null,
    ) {}
}
