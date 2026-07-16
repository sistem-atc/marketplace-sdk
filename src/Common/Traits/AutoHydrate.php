<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Common\Traits;

use BackedEnum;
use DateTimeImmutable;
use DateTimeInterface;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionNamedType;
use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;

/**
 * Hidrata o DTO a partir do array cru da resposta da API (reflection sobre o
 * construtor promovido). Fornece `fromArray()` do DTOInterface.
 *
 * Diferenca vs asaas-laravel: as APIs de marketplace sao snake_case
 * (`invoice_key`, `xml_location`), entao normalizamos a chave — o parametro do
 * DTO fica camelCase idiomatico (`$invoiceKey`) e o lookup tenta camelCase E
 * snake_case. Assim serve tanto APIs camelCase quanto snake_case sem mudar o DTO.
 *
 * Suporta: builtins (cast float/int/bool/string), enums BackedEnum (tryFrom),
 * DateTimeImmutable, DTO aninhado (`method_exists fromArray`) e listas de DTO
 * via #[ArrayOf(Classe::class)].
 */
trait AutoHydrate
{
    use StringCase;

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): static
    {
        $reflection = new ReflectionClass(static::class);
        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return new static();
        }

        $params = [];

        foreach ($constructor->getParameters() as $param) {
            $name = $param->getName();
            $type = $param->getType();

            // Lookup tolerante: camelCase (padrao do DTO) OU snake_case (API).
            $value = $data[$name] ?? $data[self::camelToSnake($name)] ?? null;

            if ($value === null) {
                $params[] = $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null;

                continue;
            }

            if ($type instanceof ReflectionNamedType) {
                $typeName = $type->getName();

                if ($type->isBuiltin()) {
                    $value = match ($typeName) {
                        'float' => is_float($value) ? $value : (float) $value,
                        'int' => is_int($value) ? $value : (int) $value,
                        'bool' => is_bool($value) ? $value : (bool) $value,
                        'string' => is_string($value) ? $value : (string) $value,
                        'array' => self::hydrateArray($param, $value, $name),
                        default => $value,
                    };
                } elseif (is_subclass_of($typeName, BackedEnum::class)) {
                    $enum = $typeName::tryFrom($value);
                    if ($enum === null) {
                        throw new InvalidArgumentException("Valor '{$value}' invalido para {$typeName} em '{$name}'");
                    }
                    $value = $enum;
                } elseif ($typeName === DateTimeInterface::class || $typeName === DateTimeImmutable::class) {
                    $value = is_string($value) ? new DateTimeImmutable($value) : $value;
                } elseif (method_exists($typeName, 'fromArray')) {
                    $value = is_array($value) ? $typeName::fromArray($value) : $value;
                }
            }

            $params[] = $value;
        }

        return new static(...$params);
    }

    /**
     * Hidrata um parametro `array`: se anotado com #[ArrayOf(DTO::class)] e o
     * valor for lista, vira lista de DTOs; senao devolve o array cru.
     */
    private static function hydrateArray(\ReflectionParameter $param, mixed $value, string $name): array
    {
        if (! is_array($value)) {
            return [];
        }

        $attributes = $param->getAttributes(ArrayOf::class);
        if (empty($attributes)) {
            return $value;
        }

        $targetClass = $attributes[0]->newInstance()->class;
        if (! method_exists($targetClass, 'fromArray')) {
            return $value;
        }

        return array_map(function ($item) use ($targetClass, $name) {
            if (! is_array($item)) {
                throw new InvalidArgumentException("Todos os itens de '{$name}' devem ser arrays; ".gettype($item)." recebido");
            }

            return $targetClass::fromArray($item);
        }, $value);
    }
}
