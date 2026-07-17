<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\DTO\Response\Product;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Bloco da descrição estendida (`field_list[]`) — a descrição da Shopee é
 * uma LISTA ordenada de blocos: `fieldType` diz se é texto ou imagem, e só
 * o campo correspondente vem preenchido.
 */
final class DescriptionField implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $fieldType = null,
        public readonly ?string $text = null,
        public readonly ?DescriptionImage $imageInfo = null,
    ) {}
}
