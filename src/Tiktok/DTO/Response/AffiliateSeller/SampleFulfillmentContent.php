<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Conteudo (video ou LIVE) que o creator postou apos receber a amostra.
 *
 * `createTime` muda de significado com o formato: em VIDEO e' quando o video
 * virou publico E foi vinculado ao produto; em LIVE e' quando o link do
 * produto entrou na transmissao. `liveEndTime` so' existe em LIVE.
 *
 * Os contadores sao ACUMULADOS, nao do periodo.
 */
final class SampleFulfillmentContent implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        // LIVE | VIDEO
        public readonly ?string $format = null,
        public readonly ?string $url = null,
        public readonly ?int $viewCount = null,
        public readonly ?int $likeCount = null,
        public readonly ?int $commentCount = null,
        public readonly ?int $paidOrderCount = null,
        public readonly ?string $pageLink = null,
        // titulo do video ou descricao da LIVE
        public readonly ?string $description = null,
        public readonly ?int $createTime = null,
        // so quando format=LIVE
        public readonly ?int $liveEndTime = null,
    ) {}
}
