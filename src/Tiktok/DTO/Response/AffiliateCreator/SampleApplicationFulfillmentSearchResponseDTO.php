<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` de POST /affiliate_creator/202409/sample_applications/fulfillments/search.
 *
 * Sem paginacao na resposta: o filtro e' so' por `fulfillment_statuses`.
 *
 * @property list<SampleApplicationFulfillment>|null $fulfillments
 */
final class SampleApplicationFulfillmentSearchResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(SampleApplicationFulfillment::class)]
        public readonly ?array $fulfillments = null,
    ) {}
}
