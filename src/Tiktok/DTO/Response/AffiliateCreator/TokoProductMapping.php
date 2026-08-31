<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * De/para de id de produto Tokopedia (`tokoPid`) <-> TikTok Shop (`ttsPid`).
 *
 * A doc declara os dois como INT e o exemplo manda numero cru — mas o
 * `tts_pid` e' snowflake de 19 digitos, que ja' aparece ARREDONDADO no proprio
 * exemplo da doc (…836402920 vira …836403000). Mantidos como INT pra espelhar
 * a API; se precisar do valor exato, leia do JSON cru.
 */
final class TokoProductMapping implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $tokoPid = null,
        public readonly ?int $ttsPid = null,
    ) {}
}
