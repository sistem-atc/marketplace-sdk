<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\CustomerService;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Conversa buyer<->shop. Não existe conversa "por pedido": o vínculo com
 * pedido nasce das mensagens (ORDER_CARD).
 */
final class Conversation implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        /** 2 = loja + comprador; 3 = com atendente na conversa. */
        public readonly ?int $participantCount = null,
        /**
         * A janela de resposta do TikTok. false = a loja NÃO pode iniciar/
         * responder (sem conversa nos últimos 30d, sem pedido nos últimos 60d
         * e sem devolução). Checar ANTES de mostrar o campo de envio na tela.
         */
        public readonly ?bool $canSendMessage = null,
        public readonly ?int $unreadCount = null,
        /** Epoch em SEGUNDOS. */
        public readonly ?int $createTime = null,
        #[ArrayOf(Participant::class)]
        public readonly ?array $participants = null,
        public readonly ?ConversationMessage $latestMessage = null,
        /** Só vem com need_session_id=true. */
        public readonly ?string $curSessionId = null,
        /** Só vem com need_session_info=true. */
        public readonly ?ConversationSession $curSession = null,
    ) {}
}
