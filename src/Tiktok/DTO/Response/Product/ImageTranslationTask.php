<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Tarefa de traducao de imagem com o resultado.
 *
 * `status`: PROCESSING | COMPLETED | FAILED. `translatedImage` so' vem em
 * COMPLETED; `failReason` e' texto livre da API (nao e' codigo — nao case
 * logica em cima dele).
 */
final class ImageTranslationTask implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $targetLanguage = null,
        public readonly ?string $status = null,
        public readonly ?string $failReason = null,
        public readonly ?TranslationImage $originalImage = null,
        public readonly ?TranslationImage $translatedImage = null,
    ) {}
}
