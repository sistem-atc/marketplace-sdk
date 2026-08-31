<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Pedido de amostra gratis feito por um creator.
 *
 * `commissionRate` aqui e' FRACAO em string ("0.1" = 10%), faixa
 * [0.01, 0.8] — diferente do resto do grupo, que usa centesimos de por cento
 * (1000 = 10%). Nao reaproveite o parser.
 *
 * Dois estados distintos convivem: `status` e' o ciclo do PEDIDO de amostra e
 * `fulfillmentStatus` e' se o creator honrou o combinado de postar conteudo.
 *
 * `partnerName` so' vem quando uma agencia pediu no lugar do creator.
 */
final class SampleApplication implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        // FRACAO em string: "0.1" = 10% (o resto do grupo usa 1000 = 10%)
        public readonly ?string $commissionRate = null,
        // PENDING | AWAITING_SHIPMENT | SHIPPED | CONTENT_PENDING | COMPLETED | *_CANCELLED | OPS_* | DEL_OPEN_COLLAB
        public readonly ?string $status = null,
        // pedido gerado quando o seller aprova
        public readonly ?string $orderId = null,
        public readonly ?int $availableQuantity = null,
        // prazo do seller pra aprovar
        public readonly ?int $approveExpirationTime = null,
        // prazo do seller pra enviar
        public readonly ?int $shipmentExpirationTime = null,
        public readonly ?string $trackingNumber = null,
        // PENDING | ONGOING | SUCCEED | FAILED | OVERDUE | SUSPEND | CANCELLED | EXEMPTED
        public readonly ?string $fulfillmentStatus = null,
        public readonly ?bool $isApprovable = null,
        public readonly ?array $disapprovableReasons = null,
        // preenchido so quando uma agencia pediu pelo creator
        public readonly ?string $partnerName = null,
        public readonly ?SampleApplicationCreator $creator = null,
        public readonly ?SampleApplicationProduct $product = null,
    ) {}
}
