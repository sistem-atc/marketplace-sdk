<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Resposta de consulta de tarefas de traducao de imagem.
 * Nao pagina — a consulta aceita no maximo 20 ids por chamada.
 *
 * @property list<ImageTranslationTask>|null $translationTasks
 */
final class ImageTranslationTaskListResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        #[ArrayOf(ImageTranslationTask::class)]
        public readonly ?array $translationTasks = null,
    ) {}
}
