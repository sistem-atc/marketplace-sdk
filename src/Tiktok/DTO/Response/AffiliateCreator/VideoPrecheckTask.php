<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Tarefa de pre-check de video, com os DOIS veredictos independentes.
 */
final class VideoPrecheckTask implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?ViolationCheckResult $violationCheckResult = null,
        public readonly ?GoodQualityCheckResult $goodQualityCheckResult = null,
    ) {}
}
