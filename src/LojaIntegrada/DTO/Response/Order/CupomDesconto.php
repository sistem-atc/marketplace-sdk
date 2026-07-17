<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\LojaIntegrada\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;


/** Cupom de desconto (cupom_desconto). Listas de escopo (categorias/clientes/...) passthrough. */
final class CupomDesconto implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $codigo = null,
        public readonly ?string $descricao = null,
        public readonly ?string $tipo = null,
        public readonly ?string $valor = null,
        public readonly ?string $valorMinimo = null,
        public readonly ?bool $ativo = null,
        public readonly ?bool $cumulativo = null,
        public readonly ?bool $aplicarNoTotal = null,
        public readonly ?string $condicaoCliente = null,
        public readonly ?string $condicaoProduto = null,
        public readonly ?int $quantidade = null,
        public readonly ?int $quantidadeUsada = null,
        public readonly ?int $quantidadePorCliente = null,
        public readonly ?string $validade = null,
        public readonly ?string $dataCriacao = null,
        public readonly ?string $dataModificacao = null,
        public readonly ?string $resourceUri = null,
        public readonly ?array $categorias = null,
        public readonly ?array $clientes = null,
        public readonly ?array $grupos = null,
        public readonly ?array $produtos = null,
        public readonly mixed $idExterno = null,
    ) {}
}
