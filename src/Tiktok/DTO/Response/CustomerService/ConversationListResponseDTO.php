<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\CustomerService;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Resposta de GET /customer_service/{v}/conversations. */
final class ConversationListResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** "" (string vazia) quando acabou a paginação — não null. */
        public readonly ?string $nextPageToken = null,
        #[ArrayOf(Conversation::class)]
        public readonly ?array $conversations = null,
    ) {}
}
