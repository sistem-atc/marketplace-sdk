<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Arquivo anexo de uma certificacao (`certifications[].files[]`).
 * O `id` e' o retornado por Upload Product File.
 *
 * @property list<string>|null $urls
 */
final class GlobalCertificationFile implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?array $urls = null,
        public readonly ?string $name = null,
        public readonly ?string $format = null,
    ) {}
}
