<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Attributes\JsonKey;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data.translation_task` do webhook TYPE 46. Uma task = 1 imagem traduzida
 * pra 1 idioma.
 *
 * DIVERGENCIA doc x exemplo: a tabela documenta `target_language`, o exemplo
 * oficial manda `target_lang`. Os dois campos existem aqui (o segundo com
 * #[JsonKey] pra fixar a chave curta) pra nao perder o idioma seja qual for a
 * forma que chegar.
 */
final class ImageTranslationTask implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        // PROCESSING | COMPLETED | FAILED
        public readonly ?string $status = null,
        // Vem "" quando a task nao falhou.
        public readonly ?string $failReason = null,
        // Forma DOCUMENTADA. de-DE | en-IE | es-ES | fr-FR | it-IT
        public readonly ?string $targetLanguage = null,
        // Forma do EXEMPLO oficial.
        #[JsonKey('target_lang')]
        public readonly ?string $targetLang = null,
        public readonly ?TranslationImage $originalImage = null,
        public readonly ?TranslationImage $translatedImage = null,
    ) {}
}
