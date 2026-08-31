<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Reverse;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Cancelamento de pedido — item de `data.cancellations[]` do Search
 * Cancellations.
 *
 * Cancelamento e devolucao sao FLUXOS SEPARADOS no TikTok (endpoints, ids e
 * buscas distintos): cancelamento acontece antes do envio, devolucao depois.
 * Quem quiser "tudo que estornou" precisa varrer os DOIS.
 *
 * `shouldReplenishStock` e' o gatilho de reposicao de estoque — vem false
 * quando a mercadoria nao volta pra nos.
 *
 * `createTime`/`updateTime` sao epoch em SEGUNDOS.
 *
 * @property list<SellerNextAction>|null $sellerNextActionResponse
 * @property list<CancelLineItem>|null $cancelLineItems
 */
final class Cancellation implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $orderId = null,
        /** CANCEL (seller/sistema) | REQUEST_CANCEL_REFUND (comprador pediu) */
        public readonly ?string $cancelType = null,
        /** CANCELLATION_REQUEST_PENDING | ... | CANCELLATION_REQUEST_SUCCESS */
        public readonly ?string $cancelStatus = null,
        /** BUYER | SELLER | SYSTEM | OPERATOR */
        public readonly ?string $role = null,
        public readonly ?string $cancelReason = null,
        public readonly ?string $cancelReasonText = null,
        public readonly ?int $createTime = null,
        public readonly ?int $updateTime = null,
        #[ArrayOf(SellerNextAction::class)]
        public readonly ?array $sellerNextActionResponse = null,
        public readonly ?RefundAmount $refundAmount = null,
        #[ArrayOf(CancelLineItem::class)]
        public readonly ?array $cancelLineItems = null,
        public readonly ?string $cancelId = null,
        public readonly ?bool $shouldReplenishStock = null,
    ) {}
}
