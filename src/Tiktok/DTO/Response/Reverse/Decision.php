<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Reverse;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Uma decisao avaliada — serve tanto o Get Decision Eligibility (202601) quanto
 * o Get Review Decision (202606), que devolvem a MESMA shape; o 202606 apenas
 * acrescenta a faixa de reembolso parcial.
 *
 * PEGADINHA: `ineligibleCode`/`ineligibleReason` vem preenchidos mesmo quando
 * `eligible` e' true (o exemplo oficial faz exatamente isso). Decida SEMPRE
 * pelo booleano `eligible`, nunca por "tem motivo de inelegibilidade".
 *
 * @property list<RejectReason>|null $availableRejectReasons
 */
final class Decision implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** APPROVE_REFUND | APPROVE_REQUEST | OFFER_PARTIAL_REFUND_AFTER_RECEIVING_PKG ... */
        public readonly ?string $decision = null,
        public readonly ?bool $eligible = null,
        public readonly ?int $ineligibleCode = null,
        public readonly ?string $ineligibleReason = null,
        #[ArrayOf(RejectReason::class)]
        public readonly ?array $availableRejectReasons = null,
        /** So' no Get Review Decision, e so' pras decisoes de reembolso parcial. */
        public readonly ?PartialRefundAmountRange $partialRefundAmountRange = null,
    ) {}
}
