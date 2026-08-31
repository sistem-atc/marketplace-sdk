<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Attributes\JsonKey;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Dados de bateria de um goods perigoso (hazmat tipo BATTERY).
 *
 * ARMADILHA DE CHAVE: `is_need_un_38_3_test` e `un_38_3_test_file_url` tem
 * underscores NO MEIO do numero da norma (UN 38.3). Nenhuma regra automatica de
 * camelCase->snake_case reproduz isso — dai o #[JsonKey] explicito.
 *
 * Pesos/capacidades sao STRING (nao float): mesmo motivo do dinheiro.
 */
final class FbtBatteryExtraInfo implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        // LITHIUM_ION | LITHIUM_METAL | OTHER
        public readonly ?string $batteryType = null,
        // STANDALONE | IN_EQUIPMENT | WITH_EQUIPMENT
        public readonly ?string $batteryPackagingType = null,
        public readonly ?string $totalBatteryWeight = null,
        public readonly ?string $totalBatteryWeightUnit = null,
        public readonly ?string $totalBatteryCapacity = null,
        public readonly ?string $totalBatteryCapacityUnit = null,
        // Contagem por embalagem — STRING na doc, nao int.
        public readonly ?string $batteryCountPerPackage = null,
        #[JsonKey('is_need_un_38_3_test')]
        public readonly ?bool $isNeedUn383Test = null,
        #[JsonKey('un_38_3_test_file_url')]
        public readonly ?string $un383TestFileUrl = null,
    ) {}
}
