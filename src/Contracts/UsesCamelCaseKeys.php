<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Contracts;

/**
 * Marca DTOs cuja API usa chaves em camelCase (ex.: Amazon Reports /
 * Notifications / Invoices 2024). O CastToArray então emite as chaves iguais
 * ao nome da propriedade (que já é camelCase), em vez do snake_case default.
 * A hidratação (AutoHydrate) já casa camelCase sem marcador (checa $data[$name]
 * primeiro). Ver [[marketplace-sdk-dto-pattern]].
 */
interface UsesCamelCaseKeys {}
