<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\LojaIntegrada\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;


/** Linha do pedido (itens[]). sku = de/para. variacao = chaves dinamicas (passthrough). */
final class Item implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $produto = null,
        public readonly ?string $produtoPai = null,
        public readonly ?string $sku = null,
        public readonly ?string $nome = null,
        public readonly ?string $tipo = null,
        // quantidade e precos sao STRING no LojaIntegrada.
        public readonly ?string $quantidade = null,
        public readonly ?string $precoCheio = null,
        public readonly ?string $precoVenda = null,
        public readonly ?string $precoSubtotal = null,
        public readonly ?string $precoPromocional = null,
        public readonly ?string $precoCusto = null,
        public readonly ?string $ncm = null,
        public readonly ?string $peso = null,
        public readonly ?int $altura = null,
        public readonly ?int $largura = null,
        public readonly ?int $profundidade = null,
        public readonly ?int $disponibilidade = null,
        public readonly ?int $linha = null,
        public readonly ?string $pedido = null,
        // variacao tem CHAVES DINAMICAS (Cor, Sabor, Tamanho...) e valor
        // ora objeto ora string — passthrough (nao tipavel como campos fixos).
        public readonly mixed $variacao = null,
    ) {}
}
