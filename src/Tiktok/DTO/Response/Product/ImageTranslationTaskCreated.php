<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Tarefa de traducao recem-criada — o eco de (imagem, idioma) com o id.
 *
 * Uma tarefa = 1 imagem para 1 idioma: pedir 2 idiomas pra mesma imagem
 * cria 2 tarefas, com ids diferentes.
 */
final class ImageTranslationTaskCreated implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $imageUri = null,
        public readonly ?string $targetLanguage = null,
    ) {}
}
