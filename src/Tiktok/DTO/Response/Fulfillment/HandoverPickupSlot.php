<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Janela de coleta oferecida pelo transportador.
 *
 * `avaliable` esta' escrito ERRADO na API (falta o "i" de available). O
 * nome do campo replica o erro de proposito — corrigir aqui faria o dado
 * sumir na hidratacao.
 */
final class HandoverPickupSlot implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $startTime = null,
        public readonly ?int $endTime = null,
        public readonly ?bool $avaliable = null,
    ) {}
}
