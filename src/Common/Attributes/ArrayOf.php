<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Common\Attributes;

use Attribute;

/**
 * Marca um parametro `array` do construtor de um DTO como uma lista de outro
 * DTO — o AutoHydrate hidrata cada item via `{class}::fromArray()`.
 *
 * Ex.: #[ArrayOf(ReferenceInvoice::class)] public readonly array $referenceInvoices = []
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
final class ArrayOf
{
    public function __construct(
        public string $class,
    ) {}
}
