<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\CustomerService;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Sessão de atendimento fechada, com as métricas de SLA que o TikTok cobra. */
final class Session implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $conversationId = null,
        /** Epoch em SEGUNDOS. */
        public readonly ?int $beginTime = null,
        public readonly ?int $endTime = null,
        public readonly ?string $buyerNickname = null,
        /** AfterSale | Logistics | Presale — lista de strings cruas. */
        public readonly ?array $chatTags = null,
        /** Nota do comprador, 1 a 5. Ausente quando não houve avaliação. */
        public readonly ?int $satisfactionScore = null,
        /** Texto no idioma do `locale` pedido. */
        public readonly ?string $dissatisfactionReason = null,
        public readonly ?bool $firstResponseLate = null,
        /** SEGUNDOS entre a 1a mensagem do comprador e a 1a resposta. */
        public readonly ?int $firstResponseTime = null,
    ) {}
}
