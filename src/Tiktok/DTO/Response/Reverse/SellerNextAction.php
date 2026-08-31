<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Reverse;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Proxima acao exigida do seller e o prazo dela.
 *
 * `deadline` e' epoch em SEGUNDOS. Se o prazo estourar sem resposta, o TikTok
 * decide sozinho — normalmente APROVANDO a devolucao. Por isso este campo vale
 * como alerta operacional, nao como informacao decorativa.
 */
final class SellerNextAction implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** SELLER_RESPOND_REFUND | SELLER_RESPOND_RETURN | SELLER_RESPOND_CANCEL ... */
        public readonly ?string $action = null,
        public readonly ?int $deadline = null,
    ) {}
}
