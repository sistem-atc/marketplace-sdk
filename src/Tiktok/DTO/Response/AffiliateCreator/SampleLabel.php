<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Etiqueta de elegibilidade de AMOSTRA de um produto pro creator logado.
 *
 * ⚠️ E' o unico endpoint que responde ANTES do pedido existir: `canApply` e
 * `status` (TO_APPLY / ONGOING / COMPLETE) dizem se aquele produto esta' no
 * fluxo de amostra pra esse creator. `reachLimit` = creator estourou a cota.
 */
final class SampleLabel implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?bool $canApply = null,
        public readonly ?string $status = null,
        public readonly ?string $applicationId = null,
        public readonly ?bool $reachLimit = null,
        public readonly ?SampleLabelProduct $sampleProduct = null,
    ) {}
}
