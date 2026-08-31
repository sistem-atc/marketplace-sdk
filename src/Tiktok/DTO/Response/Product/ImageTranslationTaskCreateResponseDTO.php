<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de criacao de tarefas de traducao de imagem.
 *
 * E' ASSINCRONA: aqui so' voltam os ids. A imagem traduzida sai no
 * Get Image Translation Tasks, quando o status virar COMPLETED.
 *
 * @property list<ImageTranslationTaskCreated>|null $translationTasks
 */
final class ImageTranslationTaskCreateResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(ImageTranslationTaskCreated::class)]
        public readonly ?array $translationTasks = null,
    ) {}
}
