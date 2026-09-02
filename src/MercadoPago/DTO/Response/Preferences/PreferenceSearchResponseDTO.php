<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\MercadoPago\DTO\Response\Preferences;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Preference Search resource. Represents the paginated result set returned when searching for
 * checkout preferences. Contains matching preference summaries along with pagination metadata.
 */
final class PreferenceSearchResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Search elements. @var list<PreferenceListResult>|null */
        #[ArrayOf(PreferenceListResult::class)]
        public readonly ?array $elements = null,

        /** Search next offset. */
        public readonly ?int $nextOffset = null,

        /** Search total. */
        public readonly ?int $total = null,
    ) {}
}
