<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Acao sugerida para ganhar pontos SKPP (`task_details[].action_suggestions[]`). */
final class SkppActionSuggestion implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $scoreIncrease = null,
        public readonly ?string $url = null,
    ) {}
}
