<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resultado da sincronizacao do produto global com UM mercado.
 *
 * ARMADILHA: e' por-mercado. A chamada retorna HTTP 200/code 0 mesmo quando
 * um mercado falhou — sempre percorra a lista checando `status`
 * (SUCCESS | FAILED | DRAFT) antes de dar a operacao por concluida.
 *
 * @property list<GlobalFailReason>|null $failReasons
 */
final class GlobalPublishResult implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $region = null,
        public readonly ?string $status = null,
        #[ArrayOf(GlobalFailReason::class)]
        public readonly ?array $failReasons = null,
    ) {}
}
