<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fbt;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Ordem de entrada de mercadoria no armazem FBT (`data.inbound_orders[]`).
 *
 * DOIS IDs PRA MESMA ORDEM: `orderId` e' so' o numero ("5766071177167344427") e
 * `orderIdString` e' o mesmo numero com prefixo IBR. TODA API deste grupo
 * (details, cancel, tracking, label) exige a forma NUMERICA — mandar o IBR
 * derruba a chamada. O prefixado serve pra exibir/casar com o painel.
 *
 * IMPACTO FISCAL: esta ordem e' a contrapartida da NOTA DE REMESSA pra armazem
 * de terceiro. A venda que sair dali depois gera a segunda nota (a de venda) —
 * mesmo par de notas do Full/ML e do FBA. `inboundGoodsLotReceiveItemDetails` e'
 * o que permite conferir a remessa contra o recebido de fato.
 *
 * Todos os *Time sao epoch em SEGUNDOS.
 *
 * @property list<FbtInboundStatusLog>|null $statusLogs
 * @property list<FbtInboundLogisticInfo>|null $logisticInfoList
 * @property list<FbtInboundCarton>|null $cartonDetails
 * @property list<FbtInboundLotReceiveItem>|null $inboundGoodsLotReceiveItemDetails
 */
final class FbtInboundOrder implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $orderId = null,
        public readonly ?string $orderIdString = null,
        public readonly ?string $orderPlanId = null,
        public readonly ?FbtMerchant $merchant = null,
        public readonly ?int $createTime = null,
        public readonly ?int $shipTime = null,
        // Prazo informado pelo SELLER, nao promessa do armazem.
        public readonly ?int $expectedArrivalTime = null,
        public readonly ?int $actualArrivalTime = null,
        public readonly ?FbtInboundWarehouse $warehouse = null,
        #[ArrayOf(FbtInboundStatusLog::class)]
        public readonly ?array $statusLogs = null,
        #[ArrayOf(FbtInboundLogisticInfo::class)]
        public readonly ?array $logisticInfoList = null,
        #[ArrayOf(FbtInboundCarton::class)]
        public readonly ?array $cartonDetails = null,
        #[ArrayOf(FbtInboundLotReceiveItem::class)]
        public readonly ?array $inboundGoodsLotReceiveItemDetails = null,
    ) {}
}
