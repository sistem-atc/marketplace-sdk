<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resultado de POST /fulfillment/202606/pod_details/get.
 *
 * `statusCode` e' um SEGUNDO codigo de status, dentro de `data`, separado do
 * `code` do envelope: zero = ok. Envelope 0 + statusCode != 0 = falhou.
 *
 * @property list<PodDetail>|null $podDetails
 */
final class PodDetailResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(PodDetail::class)]
        public readonly ?array $podDetails = null,
        public readonly ?int $statusCode = null,
    ) {}
}
