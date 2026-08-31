<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Pacote resultante de um combine/uncombine.
 *
 * O `id` e' NOVO — combinar/descombinar nao preserva o package_id anterior.
 * Qualquer etiqueta ja' impressa pro id antigo deixa de valer.
 *
 * @property list<string>|null $orderIds
 */
final class CombinedPackage implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        /** @var list<string>|null */
        public readonly ?array $orderIds = null,
    ) {}
}
