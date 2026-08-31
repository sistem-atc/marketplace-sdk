<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Reverse;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` do Get Reject Reasons (`/return_refund/202309/reject_reasons`).
 *
 * A lista e' POR devolucao/cancelamento (`return_or_cancel_id`), nao um
 * catalogo fixo da loja: nao da' pra cachear global.
 *
 * @property list<RejectReason>|null $reasons
 */
final class RejectReasonsResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(RejectReason::class)]
        public readonly ?array $reasons = null,
    ) {}
}
