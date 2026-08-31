<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Webhook;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/** Item de `data.issues[]` do topico 55. So' vem quando `result` = FAIL. */
final class VideoPrecheckIssue implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        /** Tipo do risco, texto livre em ingles ("Pirated Content"). Nao e' enum. */
        public readonly ?string $risk = null,
        /** Sugestao de correcao, texto livre pro criador. */
        public readonly ?string $suggestion = null,
    ) {}
}
