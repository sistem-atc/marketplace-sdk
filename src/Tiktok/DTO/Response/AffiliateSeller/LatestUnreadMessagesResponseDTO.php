<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` de
 * `GET /affiliate_seller/202412/conversations/messages/list/newest`.
 *
 * JANELA DE 1 MINUTO: so' devolve o que chegou no ultimo minuto. E' um sino
 * pra polling curto, nao uma caixa de entrada — quem varre de hora em hora
 * perde mensagem. O TikTok nao tem webhook de IM de afiliado.
 */
final class LatestUnreadMessagesResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(UnreadMessage::class)]
        public readonly ?array $newestMessageList = null,
    ) {}
}
