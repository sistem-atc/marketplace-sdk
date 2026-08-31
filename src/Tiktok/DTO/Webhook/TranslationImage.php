<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Imagem (original ou traduzida) do webhook TYPE 46.
 *
 * `uri` e' o handle interno do TikTok (o que se passa de volta pra API de
 * produto); `url` e' o CDN publico, que expira. Pra persistir, guarde a `uri`.
 */
final class TranslationImage implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $uri = null,
        public readonly ?string $url = null,
    ) {}
}
