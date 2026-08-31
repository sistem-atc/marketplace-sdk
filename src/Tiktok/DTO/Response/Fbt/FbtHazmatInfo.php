<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Declaracao de mercadoria perigosa do goods.
 *
 * O TikTok manda os TRES blocos "extra" (bateria, liquido inflamavel, aerossol)
 * na mesma resposta mesmo quando so' um se aplica — quem manda e' `hazmatType`.
 * Nao infira o tipo pela presenca do bloco.
 *
 * `dgClass` pode vir com quebra de linha no fim ("CLASS_2\n") no proprio
 * exemplo oficial: trim() no consumidor, nunca comparacao estrita crua.
 */
final class FbtHazmatInfo implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        // MAGNETIZED | BATTERY | FLAMMABLE_LIQUID | AEROSOLS
        public readonly ?string $hazmatType = null,
        public readonly ?FbtBatteryExtraInfo $batteryExtraInfo = null,
        public readonly ?FbtFlammableLiquidsExtraInfo $flammableLiquidsExtraInfo = null,
        public readonly ?FbtAerosolExtraInfo $aerosolExtraInfo = null,
        // CLASS_2 | CLASS_3 | CLASS_9
        public readonly ?string $dgClass = null,
        // Numero UN de 4 digitos (UN1266, UN3480...) ou OTHER
        public readonly ?string $dgUncode = null,
        // MSDS/FISPQ que sustenta a analise de conformidade no FBT
        public readonly ?string $msdsFileUrl = null,
    ) {}
}
