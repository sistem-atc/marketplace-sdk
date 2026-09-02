<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\PreApprovals;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\MercadoPago\DTO\Shared\Paging;

/**
 * PreApproval (Subscription) Search resource. Represents the paginated result set returned when
 * searching for subscriptions. Contains matching subscription records along with pagination
 * metadata.
 */
final class PreApprovalSearchResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Search paging. */
        public readonly ?Paging $paging = null,

        /** Search results. @var list<PreApprovalResponseDTO>|null */
        #[ArrayOf(PreApprovalResponseDTO::class)]
        public readonly ?array $results = null,
    ) {}
}
