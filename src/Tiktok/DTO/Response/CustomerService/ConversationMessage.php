<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\CustomerService;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Mensagem de uma conversa — a unidade que alimenta a projeção de chats
 * unificados do ERP.
 *
 * `content` NÃO é texto: é um JSON serializado cuja shape muda por `type`
 * (TEXT -> {"content":...}, IMAGE -> {url,width,height}, ORDER_CARD ->
 * {"order_id":...}, PRODUCT_CARD -> {"product_id":...}). É por ele que se
 * amarra a conversa a um pedido — não existe conversa "por pedido" no TikTok.
 * Fica string de propósito: decodificar aqui obrigaria um DTO por type e
 * perderia os types que ainda não conhecemos.
 */
final class ConversationMessage implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        /** TEXT | IMAGE | VIDEO | PRODUCT_CARD | ORDER_CARD | RETURN_REFUND_CARD | COUPON_CARD | NOTIFICATION | ALLOCATED_SERVICE | BUYER_ENTER_FROM_* | EMOTICONS | OTHER */
        public readonly ?string $type = null,
        public readonly ?string $content = null,
        /** Epoch em SEGUNDOS. */
        public readonly ?int $createTime = null,
        /**
         * false em mensagens de sistema (pedido de avaliação, por exemplo) que
         * não devem aparecer pro atendente. Respeitar isso na tela.
         */
        public readonly ?bool $isVisible = null,
        public readonly ?MessageSender $sender = null,
        /**
         * Ordenador da conversa. Cresce com o tempo mas NÃO é estritamente
         * crescente — serve pra ordenar, nunca pra contar mensagens.
         */
        public readonly ?string $index = null,
        /** JSON serializado com o detalhe estruturado do card (só com need_data=true). */
        public readonly ?string $data = null,
        /** Versão legível do card (só com need_plaintext=true). */
        public readonly ?string $plaintext = null,
    ) {}
}
