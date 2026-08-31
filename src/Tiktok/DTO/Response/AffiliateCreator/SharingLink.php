<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Link de afiliado gerado pra um material (produto/campanha).
 *
 * Tres formatos do MESMO destino: `sharingLink` (web), `deepLink` (abre o app
 * instalado) e `oneLink` (cai na loja de apps se o TikTok nao estiver
 * instalado). Pra atribuicao, troque `o_event_id` no deep/one link por um id
 * unico POR CLIQUE, sem re-encodar a URL.
 *
 * `materialId` e' STRING na doc do /general_publishers e INT na do /publisher
 * — snowflake grande, tratado como STRING nos dois.
 */
final class SharingLink implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $materialId = null,
        public readonly ?string $sharingLink = null,
        public readonly ?string $deepLink = null,
        public readonly ?string $oneLink = null,
    ) {}
}
