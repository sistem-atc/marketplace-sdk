<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Contracts;

/**
 * Marcador: o DTO usa chaves PascalCase (padrão da Amazon SP-API —
 * `AmazonOrderId`, `OrderStatus`, `OrderTotal`). AutoHydrate e CastToArray
 * convertem camelCase↔PascalCase em vez de camelCase↔snake_case.
 *
 * Siglas que a conversão automática não acerta (`SellerSKU`, `ASIN`, `IsISPU`)
 * continuam declaradas por campo com #[JsonKey('SellerSKU')].
 */
interface UsesPascalCaseKeys {}
