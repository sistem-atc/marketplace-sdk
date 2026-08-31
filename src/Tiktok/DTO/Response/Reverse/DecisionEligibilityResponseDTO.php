<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Reverse;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` do Get Decision Eligibility (`/return_refund/202601/decision_eligibility`).
 *
 * Consulte ANTES de aprovar/rejeitar: e' o unico jeito de saber se a acao ainda
 * cabe (o comprador pode ter desistido, o prazo pode ter estourado e o TikTok
 * pode ter decidido por arbitragem). Chamar a acao direto queima tentativa e
 * volta erro.
 *
 * @property list<Decision>|null $decisions
 */
final class DecisionEligibilityResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(Decision::class)]
        public readonly ?array $decisions = null,
    ) {}
}
