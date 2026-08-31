<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Reverse;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` do Review Aftersales (`/return_refund/202606/aftersales/review`).
 *
 * A resposta de SUCESSO e' quase vazia: so' `errors`, com o que falhou dentro
 * do lote. `errors` ausente = tudo passou.
 *
 * @property list<ReviewError>|null $errors
 */
final class ReviewAftersalesResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(ReviewError::class)]
        public readonly ?array $errors = null,
    ) {}
}
