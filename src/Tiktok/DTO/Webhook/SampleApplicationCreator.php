<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data.creator` do topico 56.
 *
 * ⚠️ A doc declara `[]object` (lista), mas o exemplo oficial manda UM OBJETO.
 * Seguimos o exemplo. So' vem o open_id — nome de usuario do TikTok, que a
 * descricao da doc promete, NAO esta' no payload; busque via Get Creator
 * Profile se precisar.
 */
final class SampleApplicationCreator implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $creatorOpenId = null,
    ) {}
}
