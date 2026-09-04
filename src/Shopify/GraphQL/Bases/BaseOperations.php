<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\GraphQL\Bases;

use InvalidArgumentException;
use SistemAtc\Marketplaces\Shopify\GraphQL\GraphQLClient;

/**
 * Base das classes geradas (Queries/ e Mutations/): monta o documento
 * `<op> <name>($a: T, ...) { <name>(a: $a, ...) { <selection> } }`
 * declarando SOMENTE as variaveis presentes em $args, com os tipos do schema.
 */
abstract class BaseOperations
{
    public function __construct(protected GraphQLClient $client) {}

    public function client(): GraphQLClient
    {
        return $this->client;
    }

    /**
     * @param array<string,mixed> $args
     * @param array<string,string> $types nome do argumento => tipo GraphQL
     */
    public static function document(string $operation, string $name, array $args, array $types, string $selection): string
    {
        $declarations = [];
        $usages = [];

        foreach (array_keys($args) as $arg) {
            if (! isset($types[$arg])) {
                throw new InvalidArgumentException("Argumento `{$arg}` nao existe em `{$name}` (aceitos: ".implode(', ', array_keys($types)).').');
            }
            $declarations[] = '$'.$arg.': '.$types[$arg];
            $usages[] = $arg.': $'.$arg;
        }

        $head = $operation.' '.$name.($declarations ? '('.implode(', ', $declarations).')' : '');
        $call = $name.($usages ? '('.implode(', ', $usages).')' : '');
        $selection = trim($selection);
        $body = $selection === '' ? $call : $call.' { '.$selection.' }';

        return $head.' { '.$body.' }';
    }

    /**
     * @param array<string,mixed> $args
     * @param array<string,string> $types
     * @return array<string,mixed>
     */
    protected function execute(string $operation, string $name, array $args, array $types, string $selection): array
    {
        $data = $this->client->query(static::document($operation, $name, $args, $types, $selection), $args);
        $value = $data[$name] ?? null;

        return is_array($value) ? $value : [];
    }
}
