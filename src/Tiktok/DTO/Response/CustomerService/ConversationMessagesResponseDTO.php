<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\CustomerService;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Resposta de GET /customer_service/{v}/conversations/{id}/messages. */
final class ConversationMessagesResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** "" (string vazia) quando acabou a paginação. */
        public readonly ?string $nextPageToken = null,
        /**
         * Texto pronto, no `locale` pedido, pra exibir no lugar de mensagem de
         * tipo que o app não sabe renderizar. Sem ele a tela mostra vazio.
         */
        public readonly ?string $unsupportedMsgTips = null,
        #[ArrayOf(ConversationMessage::class)]
        public readonly ?array $messages = null,
    ) {}
}
