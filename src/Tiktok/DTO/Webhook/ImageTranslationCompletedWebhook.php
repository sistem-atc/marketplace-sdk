<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Conteudo de `data` do webhook TYPE 46 — Image translation completed.
 *
 * Vale so' pra vendedor que opera na UE. Dispara tanto no sucesso quanto na
 * falha: `translationTask->status` e' quem diz qual dos dois.
 *
 * NAO traz product_id: o vinculo com o produto e' feito pelo id da task que o
 * proprio app pediu.
 */
final class ImageTranslationCompletedWebhook implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?ImageTranslationTask $translationTask = null,
    ) {}
}
