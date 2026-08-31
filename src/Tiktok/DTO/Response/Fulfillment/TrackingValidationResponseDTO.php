<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resultado de GET /fulfillment/202508/tts_tracking_validation.
 *
 * Sao DUAS coisas distintas: `isTiktokShipping` = a etiqueta/frete e' da
 * plataforma; `isTiktokCollection` = a COLETA e' da plataforma. Um pode ser
 * true sem o outro.
 */
final class TrackingValidationResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?bool $isTiktokShipping = null,
        public readonly ?bool $isTiktokCollection = null,
    ) {}
}
