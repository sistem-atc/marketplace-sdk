<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\CustomerEngagement;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Quantos e quais cupons o template aceita na mensagem. */
final class CouponCardRules implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $minCount = null,
        public readonly ?int $maxCount = null,
        /**
         * REGULAR_ALL | REGULAR_REPEAT | ... — restringe QUAIS cupons podem
         * entrar: mandar um cupom fora desta lista faz a task falhar na hora
         * do envio, não na criação.
         */
        public readonly ?array $couponType = null,
    ) {}
}
