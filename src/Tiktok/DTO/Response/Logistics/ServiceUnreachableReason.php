<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Logistics;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Serviço do template que NÃO atende o produto consultado, com os motivos.
 * Se a lista `serviceUnreachableReason` do template vier vazia, o template é
 * utilizável.
 *
 * @property list<TemplateFilterReason>|null $filterReason
 */
final class ServiceUnreachableReason implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $serviceId = null,
        #[ArrayOf(TemplateFilterReason::class)]
        public readonly ?array $filterReason = null,
    ) {}
}
