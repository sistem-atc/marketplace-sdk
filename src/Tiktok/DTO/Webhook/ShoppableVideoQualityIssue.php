<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Item de `data.good_quality_check_result.issues[]` do topico 59.
 *
 * Shape DIFERENTE do issue de violacao: aqui o identificador e' `code`
 * ("LOW_RESOLUTION"), nao `risk`. Sao dois blocos de checagem independentes.
 */
final class ShoppableVideoQualityIssue implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Codigo de qualidade ("LOW_RESOLUTION"). Texto livre na doc. */
        public readonly ?string $code = null,
        public readonly ?string $suggestions = null,
    ) {}
}
