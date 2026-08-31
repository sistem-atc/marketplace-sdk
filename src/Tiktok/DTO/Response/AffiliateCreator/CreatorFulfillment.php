<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Contrapartida de conteudo que o creator assumiu ao receber a amostra.
 *
 * `expirationTime` = {recebimento da amostra} + 14 dias (+ suspensoes).
 * Datas em epoch de SEGUNDOS; `totalSuspendDuration` tambem em segundos.
 */
final class CreatorFulfillment implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?int $expirationTime = null,
        public readonly ?int $totalSuspendDuration = null,
        public readonly ?string $status = null,
        public readonly ?string $boundProductStatus = null,
    ) {}
}
