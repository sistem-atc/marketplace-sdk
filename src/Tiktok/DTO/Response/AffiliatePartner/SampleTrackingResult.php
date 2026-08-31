<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliatePartner;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Evento de rastreio da amostra enviada ao criador. */
final class SampleTrackingResult implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** STRING com epoch — a doc não declara a unidade. */
        public readonly ?string $trackingEventUpdateDate = null,
        /** Chave de enum, não texto pro usuário: THE_PACKAGE_HAS_BEEN_DELIVERED, OUT_FOR_DELIVERY, … */
        public readonly ?string $trackingEventDescription = null,
        public readonly ?string $trackingEventDescriptionExtended = null,
    ) {}
}
