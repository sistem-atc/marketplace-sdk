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
        $keys = $this->jsonKeyMap();
        $pascal = $this instanceof \SistemAtc\Marketplaces\Contracts\UsesPascalCaseKeys;
        $camel = $this instanceof \SistemAtc\Marketplaces\Contracts\UsesCamelCaseKeys;

        foreach (get_object_vars($this) as $key => $value) {
            $formatted = $this->formatValue($value);
            if ($formatted !== null) {
                // #[JsonKey] manda; senao PascalCase (Amazon Orders/Pricing),
                // camelCase (Amazon Reports/Notifications) ou snake_case (default).
                $result[$keys[$key] ?? match (true) {
                    $pascal => $this->camelToPascal($key),
                    $camel => $key,
                    default => $this->camelToSnake($key),
                }] = $formatted;
            }
        }

        return $result;
    }

    /**
     * Mapa propriedade => chave declarada em #[JsonKey] (so' as que tem).
     *
     * Le do CONSTRUTOR: os DTOs usam promocao de propriedade, e o atributo
     * fica no parametro.
     *
     * @return array<string, string>
     */
    private function jsonKeyMap(): array
    {
        $ctor = (new \ReflectionClass($this))->getConstructor();
        if ($ctor === null) {
            return [];
        }

        $map = [];
        foreach ($ctor->getParameters() as $param) {
            $attrs = $param->getAttributes(\SistemAtc\Marketplaces\Common\Attributes\JsonKey::class);
            if ($attrs !== []) {
                $map[$param->getName()] = $attrs[0]->newInstance()->key;
            }
        }

        return $map;
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
