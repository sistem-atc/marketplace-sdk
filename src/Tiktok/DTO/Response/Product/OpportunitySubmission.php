<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Registro de submissao de produto para uma oportunidade.
 *
 * Serve tanto ao Submit (que devolve so' id/opportunity_id/product_id/status/
 * submit_time/rejection_reason) quanto ao Get Submission Records (que devolve
 * tudo). Status: PENDING_REVIEW / UNDER_REVIEW / APPROVED / REJECTED.
 */
final class OpportunitySubmission implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $opportunityId = null,
        public readonly ?string $opportunityTitle = null,
        public readonly ?string $status = null,
        public readonly ?string $productId = null,
        public readonly ?string $productName = null,
        public readonly ?int $submitTime = null,
        public readonly ?int $reviewTime = null,
        public readonly ?string $rejectionReason = null,
        public readonly ?int $productOrders = null,
        public readonly ?int $productPv = null,
    ) {}
}
