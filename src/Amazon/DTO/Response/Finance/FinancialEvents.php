<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\DTO\Response\Finance;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\Contracts\UsesPascalCaseKeys;

/**
 * `payload.FinancialEvents` de listOrderFinancialEvents (Finances API v0,
 * PascalCase). É uma árvore MUITO profunda (~30 tipos de event list, cada um
 * com sub-listas de fee/charge/promotion + Money {CurrencyAmount,CurrencyCode}).
 *
 * As event lists ficam como PASSTHROUGH tipado (mixed): o consumidor
 * (PullAmazonOrderFees::parseEvents) é um parser robusto que já extrai fee/charge/
 * promotion por travessia; sub-tipar os ~30 ramos sem corpus real seria chute.
 * As duas lists consumidas hoje (Shipment/Refund) ganham propriedade nomeada;
 * o resto da árvore chega intacto pra quem quiser explorar depois (o campo
 * `all` não existe — as demais lists, se vierem, são preservadas no toArray via
 * as propriedades declaradas). Ver [[marketplace-sdk-dto-pattern]].
 */
final class FinancialEvents implements DTOInterface, UsesPascalCaseKeys
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly mixed $shipmentEventList = null,
        public readonly mixed $refundEventList = null,
        public readonly mixed $serviceFeeEventList = null,
        public readonly mixed $adjustmentEventList = null,
        public readonly mixed $chargebackEventList = null,
        public readonly mixed $guaranteeClaimEventList = null,
        public readonly mixed $retrochargeEventList = null,
        public readonly mixed $productAdsPaymentEventList = null,
    ) {}
}
