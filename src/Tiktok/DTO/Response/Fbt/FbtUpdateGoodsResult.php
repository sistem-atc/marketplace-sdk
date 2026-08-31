<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resultado da atualizacao de um goods.
 *
 * Ao contrario do create, aqui `update_result_info` e' um OBJETO unico (o
 * endpoint atualiza um goods por chamada), nao uma lista.
 */
final class FbtUpdateGoodsResult implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $ttsGoodsId = null,
        public readonly ?bool $isSuccess = null,
        public readonly ?string $failReason = null,
    ) {}
}
