<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\CustomerService;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Participante de uma conversa (comprador, loja ou atendente).
 *
 * ARMADILHA: `im_user_id` é o id do participante DENTRO do sistema de IM e NÃO
 * casa com nenhum pedido. Quem quiser cruzar a conversa com `orders` tem que
 * usar `user_id` — que é o mesmo `data.orders.user_id` do Get Order Detail.
 * O webhook type 33 só entrega `sender_im_user_id`, então a identidade real do
 * comprador só chega por aqui.
 */
final class Participant implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $imUserId = null,
        public readonly ?string $avatar = null,
        /** Id que casa com pedidos. Não confundir com `imUserId`. */
        public readonly ?string $userId = null,
        /** BUYER | SHOP | CUSTOMER_SERVICE */
        public readonly ?string $role = null,
        public readonly ?string $nickname = null,
        /**
         * TIKTOK_SHOP | TOKOPEDIA. Só vem quando role=BUYER e a região é
         * Indonésia; no Brasil é sempre null. Get Conversations manda como
         * `buyer_platform` e Get Conversation como `platform` — o TikTok
         * renomeou o campo entre as versões e mantemos os dois pra não perder
         * nenhum dos dois formatos.
         */
        public readonly ?string $buyerPlatform = null,
        public readonly ?string $platform = null,
    ) {}
}
