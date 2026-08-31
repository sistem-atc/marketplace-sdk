<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Reverse;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Elegibilidade de UM tipo de pos-venda (`line_item_eligibility[]`).
 *
 * A resposta NAO e' "pode ou nao pode devolver": e' uma linha POR
 * `request_type` (CANCEL, RETURN, RETURN_AND_REFUND — a doc lista RETURN aqui
 * e REFUND no exemplo; o TikTok usa os dois nomes). Quem le so' a primeira
 * linha conclui errado.
 *
 * `eligible=false` sempre vem acompanhado de `ineligible_code`/`ineligible_reason`.
 *
 * `availableReasonNames` e' tipado `array|string`: a doc declara `[]string`
 * mas o exemplo OFICIAL manda a string crua ("available reason names").
 * Tipar so' array faria o valor sumir em silencio quando a API mandar escalar.
 *
 * @property list<AftersaleEligibilityOrderLine>|null $orderLineList
 */
final class LineItemAftersaleEligibility implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $requestType = null,
        /** Campo LEGADO — integracao nova usa `orderLineList`. @var list<string>|null */
        public readonly ?array $orderLineItemsIds = null,
        public readonly ?bool $eligible = null,
        public readonly ?int $ineligibleCode = null,
        public readonly ?string $ineligibleReason = null,
        /** Chaves de motivo aceitas ao abrir o pos-venda — nunca o texto traduzido. */
        public readonly array|string|null $availableReasonNames = null,
        #[ArrayOf(AftersaleEligibilityOrderLine::class)]
        public readonly ?array $orderLineList = null,
    ) {}
}
