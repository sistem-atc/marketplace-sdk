<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data.violation_check_result` do topico 59.
 *
 * `status` = PROCESSING significa que o veredito AINDA NAO SAIU — o webhook
 * pode chegar antes da conclusao. Nao trate "nao e' SUCCESS" como reprovado.
 */
final class ShoppableVideoViolationCheckResult implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    /** @param list<ShoppableVideoViolationIssue>|null $issues */
    public function __construct(
        /** SUCCESS | FAIL | PROCESSING */
        public readonly ?string $status = null,
        #[ArrayOf(ShoppableVideoViolationIssue::class)]
        public readonly ?array $issues = null,
    ) {}
}
