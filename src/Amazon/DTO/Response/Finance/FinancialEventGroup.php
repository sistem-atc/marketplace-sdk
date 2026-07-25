<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\DTO\Response\Finance;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\Contracts\UsesPascalCaseKeys;

/**
 * Um GRUPO de eventos financeiros = um SETTLEMENT (repasse) da Amazon.
 *
 * É o repasse REAL: `originalTotal` é o valor efetivamente depositado, e
 * `fundTransferStatus`/`fundTransferDate`/`traceId` dizem se e quando caiu no
 * banco (rastro pra conciliação bancária). Diferente do net por-pedido (que é
 * calculado principal−taxas), aqui é o número que a Amazon de fato transferiu.
 *
 * Os campos de dinheiro (`originalTotal`, `convertedTotal`, `beginningBalance`)
 * vêm como `{CurrencyCode, CurrencyAmount}` — mantidos crus (mixed) pro
 * consumidor ler `CurrencyAmount` (mesma convenção do FinancialEvents).
 */
final class FinancialEventGroup implements DTOInterface, UsesPascalCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $financialEventGroupId = null,
        public readonly ?string $processingStatus = null,
        public readonly ?string $fundTransferStatus = null,
        public readonly mixed $originalTotal = null,
        public readonly mixed $convertedTotal = null,
        public readonly ?string $fundTransferDate = null,
        public readonly ?string $traceId = null,
        public readonly ?string $accountTail = null,
        public readonly mixed $beginningBalance = null,
        public readonly ?string $financialEventGroupStart = null,
        public readonly ?string $financialEventGroupEnd = null,
    ) {}
}
