<?php

declare(strict_types=1);

use SistemAtc\Marketplaces\Shopify\GraphQL\Bases\BaseOperations;

/** @return array<int,string> nomes dos metodos publicos declarados nas classes de $dir */
function shopifyGqlCoverageMethods(string $dir): array
{
    $names = [];
    foreach (glob(__DIR__."/../../src/Shopify/GraphQL/{$dir}/*.php") as $file) {
        $class = 'SistemAtc\\Marketplaces\\Shopify\\GraphQL\\'.$dir.'\\'.basename($file, '.php');
        $ref = new ReflectionClass($class);
        expect($ref->isSubclassOf(BaseOperations::class))->toBeTrue();
        foreach ($ref->getMethods(ReflectionMethod::IS_PUBLIC) as $m) {
            if ($m->getDeclaringClass()->getName() === $class && ! $m->isConstructor()) {
                $names[] = $m->getName();
            }
        }
    }

    return $names;
}

function shopifyGqlCoverageOps(): array
{
    return json_decode((string) file_get_contents(__DIR__.'/../Fixtures/Shopify/graphql_ops_2026-07.json'), true);
}

describe('Shopify GraphQL — cobertura do schema 2026-07', function () {
    it('tem um metodo por query (287)', function () {
        $expected = array_column(shopifyGqlCoverageOps()['queries'], 'name');
        $methods = shopifyGqlCoverageMethods('Queries');

        expect($expected)->toHaveCount(287)
            ->and(count($methods))->toBe(count($expected))
            ->and(array_diff($expected, $methods))->toBe([])
            ->and(array_diff($methods, $expected))->toBe([]);
    });

    it('tem um metodo por mutation (523)', function () {
        $expected = array_column(shopifyGqlCoverageOps()['mutations'], 'name');
        $methods = shopifyGqlCoverageMethods('Mutations');

        expect($expected)->toHaveCount(523)
            ->and(count($methods))->toBe(count($expected))
            ->and(array_diff($expected, $methods))->toBe([])
            ->and(array_diff($methods, $expected))->toBe([]);
    });

    it('marca @deprecated exatamente nas operacoes deprecated do schema', function () {
        $ops = shopifyGqlCoverageOps();
        foreach ([['Queries', $ops['queries']], ['Mutations', $ops['mutations']]] as [$dir, $list]) {
            $deprecated = array_column(array_filter($list, fn ($o) => $o['deprecated']), 'name');
            $found = [];
            foreach (glob(__DIR__."/../../src/Shopify/GraphQL/{$dir}/*.php") as $file) {
                $class = 'SistemAtc\\Marketplaces\\Shopify\\GraphQL\\'.$dir.'\\'.basename($file, '.php');
                foreach ((new ReflectionClass($class))->getMethods(ReflectionMethod::IS_PUBLIC) as $m) {
                    if (str_contains((string) $m->getDocComment(), '@deprecated')) {
                        $found[] = $m->getName();
                    }
                }
            }
            sort($deprecated);
            sort($found);
            expect($found)->toBe($deprecated);
        }
    });
});
