<?php

declare(strict_types=1);

use SistemAtc\Marketplaces\LojaIntegrada\DTO\Response\Order\Item;
use SistemAtc\Marketplaces\LojaIntegrada\DTO\Response\Order\OrderResponseDTO;
use SistemAtc\Marketplaces\LojaIntegrada\DTO\Response\Order\OrderSearchResponseDTO;
use SistemAtc\Marketplaces\LojaIntegrada\DTO\Response\Order\Pagamento;

/**
 * Payload SINTETICO na shape da API LojaIntegrada (`pedido/{numero}/`) — dados
 * fake. Cobre os traços reais: chaves em PORTUGUÊS, DINHEIRO como STRING,
 * `itens[].variacao` com chaves DINÂMICAS (passthrough), pagamento com
 * parcelamento/pix.
 */
function fakeLiOrderPayload(): array
{
    return [
        'id' => 122551114,
        'numero' => 15230,
        'id_externo' => null,
        'valor_total' => '859.60',
        'valor_subtotal' => '859.60',
        'valor_desconto' => '0.00',
        'valor_envio' => '0.00',
        'peso_real' => '0.9',
        'data_criacao' => '2026-05-04T12:28:18.471052-03:00',
        'data_modificacao' => '2026-05-08T16:18:18.498489-03:00',
        'resource_uri' => '/api/v1/pedido/15230',
        'situacao' => [
            'id' => 14,
            'codigo' => 'pedido_entregue',
            'nome' => 'Pedido Entregue',
            'aprovado' => true,
            'cancelado' => false,
            'final' => true,
            'notificar_comprador' => true,
        ],
        'cliente' => [
            'id' => 91367652,
            'nome' => 'Fulana Fake',
            'email' => 'fake@example.com',
            'cpf' => '00000000000',
            'cnpj' => null,
            'telefone_celular' => '11900000000',
        ],
        'itens' => [[
            'id' => 271009161,
            'sku' => 'TOP-0003-0007-P',
            'nome' => 'T-Shirt Cropped Preto',
            'produto' => '/api/v1/produto/347933894',
            'quantidade' => '1.0000',
            'preco_cheio' => '129.90',
            'preco_venda' => '129.90',
            'preco_subtotal' => '129.90',
            'preco_promocional' => null,
            // variacao: chaves DINAMICAS, valor ora objeto ora string.
            'variacao' => ['Cor' => 'Preto', 'Tamanho' => 'P'],
        ]],
        'pagamentos' => [[
            'id' => 122440436,
            'valor' => '859.60',
            'valor_pago' => '859.60',
            'pagamento_tipo' => 'pix',
            'transacao_id' => 'txn_fake_123',
            'pix_code' => '00020126...fake',
            'pix_qrcode' => null,
            'parcelamento' => ['numero_parcelas' => 1, 'valor_parcela' => 859.60],
        ]],
        'envios' => [[
            'id' => 122440313,
            'valor' => null,
            'objeto' => 'SSDCW000063',
            'forma_envio' => ['id' => 32455, 'nome' => 'Envio Externo', 'code' => 'ENVIOEXTERNO'],
        ]],
    ];
}

it('hidrata a árvore do pedido LI com acesso tipado', function () {
    $o = OrderResponseDTO::fromArray(fakeLiOrderPayload());

    expect($o->id)->toBe(122551114)
        ->and($o->numero)->toBe(15230)
        ->and($o->valorTotal)->toBe('859.60')          // dinheiro STRING
        ->and($o->situacao->codigo)->toBe('pedido_entregue')
        ->and($o->situacao->aprovado)->toBeTrue()
        ->and($o->situacao->cancelado)->toBeFalse()
        ->and($o->cliente->nome)->toBe('Fulana Fake')
        ->and($o->cliente->cpf)->toBe('00000000000')
        ->and($o->envios[0]->objeto)->toBe('SSDCW000063')
        ->and($o->envios[0]->formaEnvio->code)->toBe('ENVIOEXTERNO')
        ->and($o->envios[0]->formaEnvio->nome)->toBe('Envio Externo');
});

it('tipa itens com dinheiro-string e variacao passthrough (chaves dinâmicas)', function () {
    $it = OrderResponseDTO::fromArray(fakeLiOrderPayload())->itens[0];

    expect($it)->toBeInstanceOf(Item::class)
        ->and($it->sku)->toBe('TOP-0003-0007-P')
        ->and($it->quantidade)->toBe('1.0000')
        ->and($it->precoVenda)->toBe('129.90')
        ->and($it->precoPromocional)->toBeNull()
        // variacao preservada verbatim (chaves dinâmicas nunca viram DTO)
        ->and($it->variacao)->toBe(['Cor' => 'Preto', 'Tamanho' => 'P']);
});

it('tipa pagamento com parcelamento e campos pix', function () {
    $p = OrderResponseDTO::fromArray(fakeLiOrderPayload())->pagamentos[0];

    expect($p)->toBeInstanceOf(Pagamento::class)
        ->and($p->pagamentoTipo)->toBe('pix')
        ->and($p->transacaoId)->toBe('txn_fake_123')
        ->and($p->pixCode)->toBe('00020126...fake')
        ->and($p->parcelamento->numeroParcelas)->toBe(1)
        // valor_parcela vem double OU int → mixed, preservado
        ->and($p->parcelamento->valorParcela)->toBe(859.60);
});

it('OrderSearchResponseDTO tipa meta Tastypie e preserva objects finos como passthrough', function () {
    // A LISTAGEM Tastypie e' FINA: relacoes vem como resource_uri STRING, nao
    // como objeto aninhado. objects e' passthrough cru (nao estoura no DTO cheio).
    $thin = ['id' => 122551114, 'numero' => 15230, 'cliente' => '/api/v1/cliente/91367652', 'situacao' => '/api/v1/situacao/14'];
    $resp = OrderSearchResponseDTO::fromArray([
        'meta' => ['limit' => 50, 'offset' => 0, 'total_count' => 15230, 'next' => null, 'previous' => null],
        'objects' => [$thin, $thin],
    ]);

    expect($resp->meta->totalCount)->toBe(15230)
        ->and($resp->meta->limit)->toBe(50)
        ->and($resp->objects)->toHaveCount(2)
        // cliente STRING resource_uri preservado verbatim (nao vira Cliente DTO)
        ->and($resp->objects[0])->toBe($thin)
        ->and($resp->objects[0]['cliente'])->toBe('/api/v1/cliente/91367652');
});

it('OrderSearchResponseDTO aguenta busca vazia', function () {
    $resp = OrderSearchResponseDTO::fromArray(['meta' => ['total_count' => 0], 'objects' => []]);

    expect($resp->objects)->toBe([])->and($resp->meta->totalCount)->toBe(0);
});

it('roundtrip fromArray→toArray preserva a árvore (lossless)', function () {
    $payload = fakeLiOrderPayload();
    $back = OrderResponseDTO::fromArray($payload)->toArray();

    expect($back['id'])->toBe($payload['id'])
        ->and($back['valor_total'])->toBe($payload['valor_total'])
        ->and($back['situacao']['codigo'])->toBe($payload['situacao']['codigo'])
        ->and($back['itens'][0]['preco_venda'])->toBe($payload['itens'][0]['preco_venda'])
        ->and($back['itens'][0]['variacao'])->toBe($payload['itens'][0]['variacao'])
        ->and($back['pagamentos'][0]['parcelamento']['numero_parcelas'])->toBe(1);
});
