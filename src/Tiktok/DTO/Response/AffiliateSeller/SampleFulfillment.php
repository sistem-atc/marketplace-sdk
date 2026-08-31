<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateSeller;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Uma entrega de contrapartida: produto + conteudo publicado.
 */
final class SampleFulfillment implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?SampleFulfillmentProduct $product = null,
        public readonly ?SampleFulfillmentContent $content = null,
    ) {}
}
