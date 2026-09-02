<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\PreApprovalPlans;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\MercadoPago\DTO\Shared\Paging;

/**
 * PreApproval Plan (Subscription Plan) Search resource. Represents the paginated result set
 * returned when searching for subscription plans. Contains matching plan records along with
 * pagination metadata.
 */
final class PreApprovalPlanSearchResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Search paging. */
        public readonly ?Paging $paging = null,

        /** Search results. @var list<PreApprovalPlanResponseDTO>|null */
        #[ArrayOf(PreApprovalPlanResponseDTO::class)]
        public readonly ?array $results = null,
    ) {}
}
