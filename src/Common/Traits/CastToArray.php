<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Common\Traits;

use BackedEnum;
use DateTimeInterface;

/**
 * Serializa o DTO de volta pra array (chaves snake_case, espelhando a shape da
 * API). Fornece `toArray()` do DTOInterface. Aninha DTOs/listas de DTO, resolve
 * BackedEnum->value e DateTime->ISO8601. Omite chaves nulas.
 */
trait CastToArray
{
    use StringCase;

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        $result = [];

        foreach (get_object_vars($this) as $key => $value) {
            $formatted = $this->formatValue($value);
            if ($formatted !== null) {
                $result[$this->camelToSnake($key)] = $formatted;
            }
        }

        return $result;
    }

    private function formatValue(mixed $value): mixed
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof BackedEnum) {
            return $value->value;
        }

        if ($value instanceof DateTimeInterface) {
            return $value->format(DateTimeInterface::ATOM);
        }

        if (is_object($value) && method_exists($value, 'toArray')) {
            return $value->toArray();
        }

        if (is_array($value)) {
            return array_map(fn ($item) => $this->formatValue($item), $value);
        }

        return $value;
    }
}
