<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Lista de URLs de uma midia (capa ou audio). O TikTok manda VARIAS urls
 * equivalentes (CDNs diferentes) — use a primeira que responder.
 *
 * @property list<string>|null $urlList
 */
final class MusicUrlList implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?array $urlList = null,
    ) {}
}
