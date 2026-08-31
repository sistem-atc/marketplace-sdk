<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Logistics;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Item de `data.templates[]` do Get Available Shipping Template.
 *
 * O endpoint devolve TODOS os templates do seller — os utilizáveis e os não
 * utilizáveis. `templateIsAvailable` é o que separa; quando false, o porquê
 * está em `serviceUnreachableReason[].filterReason[]`. Filtrar a lista sem
 * olhar essa flag entrega template que o TikTok vai recusar na hora de enviar.
 *
 * @property list<ServiceUnreachableReason>|null $serviceUnreachableReason
 */
final class AvailableShippingTemplate implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?bool $templateIsAvailable = null,
        public readonly ?ShippingTemplate $template = null,
        #[ArrayOf(ServiceUnreachableReason::class)]
        public readonly ?array $serviceUnreachableReason = null,
    ) {}
}
