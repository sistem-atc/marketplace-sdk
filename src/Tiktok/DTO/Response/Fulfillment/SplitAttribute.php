<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Se um pedido pode/deve ser dividido em varios pacotes.
 *
 * `canSplit` e `mustSplit` sao INDEPENDENTES: pedido pode ter mustSplit=true
 * e canSplit=false (obrigado a dividir, mas bloqueado agora). `reason` so'
 * vem quando canSplit=false; `mustSplitReasons` so' quando mustSplit=true.
 *
 * @property list<MustSplitReason>|null $mustSplitReasons
 */
final class SplitAttribute implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $orderId = null,
        public readonly ?bool $canSplit = null,
        public readonly ?string $reason = null,
        public readonly ?bool $mustSplit = null,
        #[ArrayOf(MustSplitReason::class)]
        public readonly ?array $mustSplitReasons = null,
    ) {}
}
