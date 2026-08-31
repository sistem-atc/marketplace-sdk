<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Semana de chegada disponivel pro inbound.
 *
 * Os dois timestamps sao epoch em segundos MAS chegam como STRING, e o proximo
 * passo (`getInboundMethodDetail` / `confirmInboundMethod`) exige que o valor
 * volte IDENTICO ao recebido. Converter pra int e reserializar muda a
 * representacao e a API recusa — por isso ficam string ponta a ponta.
 *
 * Sao sempre domingo a' meia-noite no fuso do OESTE dos EUA, nao UTC.
 */
final class FbtTimeWindow implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $startTimestamp = null,
        public readonly ?string $endTimestamp = null,
    ) {}
}
