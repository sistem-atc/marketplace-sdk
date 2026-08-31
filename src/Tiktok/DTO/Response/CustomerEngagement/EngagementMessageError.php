<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\CustomerEngagement;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Falha de envio POR DESTINATÁRIO (não é erro da requisição). */
final class EngagementMessageError implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $code = null,
        public readonly ?string $message = null,
        public readonly ?EngagementMessageErrorDetail $detail = null,
    ) {}
}
