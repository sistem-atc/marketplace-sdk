<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resultado da checagem de POLITICA: SUCCESS | FAIL | PROCESSING.
 * PROCESSING = ainda rodando; consulte de novo em vez de tratar como falha.
 *
 * @property list<ViolationIssue>|null $issues
 */
final class ViolationCheckResult implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $status = null,
        #[ArrayOf(ViolationIssue::class)]
        public readonly ?array $issues = null,
    ) {}
}
