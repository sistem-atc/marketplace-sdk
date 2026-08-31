<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` de remover (POST 202409 /showcases/products) e fixar no topo
 * (POST 202409 /showcases/products/top) — os dois devolvem o MESMO envelope
 * repetido DENTRO de `data`: code/message/request_id de novo.
 *
 * Nao e' redundancia inofensiva: o `code` interno pode divergir do externo,
 * entao confira este aqui pra saber se a operacao valeu.
 */
final class ShowcaseOperationResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $code = null,
        public readonly ?string $message = null,
        public readonly ?string $requestId = null,
    ) {}
}
