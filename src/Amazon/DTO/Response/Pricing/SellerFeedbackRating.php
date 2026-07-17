<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\DTO\Response\Pricing;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\Contracts\UsesPascalCaseKeys;

/** `Offers[].SellerFeedbackRating`. */
final class SellerFeedbackRating implements DTOInterface, UsesPascalCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $feedbackCount = null,
        // int (92) OU float (92.5) — mixed preserva no roundtrip.
        public readonly mixed $sellerPositiveFeedbackRating = null,
    ) {}
}
