<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Item de `data.violation_check_result.issues[]` do topico 59.
 *
 * ⚠️ A chave e' `suggestions` (PLURAL) aqui, contra `suggestion` (singular) no
 * topico 55. Reusar o DTO do 55 perderia o campo em silencio.
 */
final class ShoppableVideoViolationIssue implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Tipo/severidade do risco ("HIGH", "Pirated Content"). Texto livre. */
        public readonly ?string $risk = null,
        public readonly ?string $suggestions = null,
    ) {}
}
