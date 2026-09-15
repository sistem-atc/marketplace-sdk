<?php

declare(strict_types=1);

use SistemAtc\Marketplaces\MercadoPago\Endpoints\Settlement\SettlementMethods;

/**
 * A config do release report decide o que o MP entrega — e o relatorio MINIMO
 * nao traz a referencia do pedido nem o tipo de registro. Numa conta assim o
 * dinheiro chega anonimo (medido em 15/09/2026: 3.567 liberacoes, ZERO casando
 * com pedido) e, sem RECORD_TYPE, agregado de saldo entra como movimento e
 * dobra a soma.
 */
it('recomenda as colunas de que o parser depende', function () {
    expect(SettlementMethods::COLUNAS_RECOMENDADAS)
        ->toContain('EXTERNAL_REFERENCE')
        ->toContain('RECORD_TYPE');
});

it('RECORD_TYPE esta na lista — sem ela agregado de saldo vira movimento', function () {
    // Documenta o porque: e' a coluna menos obvia e a mais cara de esquecer.
    expect(SettlementMethods::COLUNAS_RECOMENDADAS)->toContain('RECORD_TYPE');
});
