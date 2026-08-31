<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de `GET /fbt/202601/merchants/mcf_status`.
 *
 * MCF = usar o estoque parado no armazem do TikTok pra atender pedido de OUTRO
 * canal. Do ponto de vista fiscal e' o caso mais delicado do grupo: a saida
 * acontece de um armazem de terceiro, entao a nota de venda do canal externo
 * tem que sair com o endereco do armazem FBT como local de retirada.
 */
final class FbtMerchantMcfStatusResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?FbtMcfStatus $mcfStatus = null,
    ) {}
}
