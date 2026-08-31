<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resultado da criacao de UM goods.
 *
 * SUCESSO E' POR LINHA, nao pela chamada: o HTTP volta code 0 mesmo quando
 * todos os itens falharam. Sempre teste `isSuccess` item a item — o exemplo
 * OFICIAL da doc traz `is_success: false` junto de um `tts_goods_id`
 * preenchido, entao a presenca do id NAO prova que criou.
 */
final class FbtCreateGoodsResult implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $ttsSkuId = null,
        public readonly ?bool $isSuccess = null,
        public readonly ?string $ttsGoodsId = null,
        public readonly ?string $failReason = null,
    ) {}
}
