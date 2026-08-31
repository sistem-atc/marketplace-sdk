<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` de `GET /affiliate_seller/202512/sample_applications/deeplink`.
 *
 * E' um deeplink `snssdk1180://` que abre o app do TikTok — nao uma URL http
 * que de' pra abrir no navegador. Validade padrao 7 dias (max 14, min 1).
 */
final class SampleRequestDeeplinkResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $deeplink = null,
    ) {}
}
