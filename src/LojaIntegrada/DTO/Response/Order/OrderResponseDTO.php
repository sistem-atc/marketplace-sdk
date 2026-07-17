<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\LojaIntegrada\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Attributes\ArrayOf;
use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Pedido LojaIntegrada — resposta de `getOrderByNumero` / item de `listOrders`.
 *
 * Plataforma BR: chaves em PORTUGUÊS. DINHEIRO é STRING (`valor_total`,
 * `preco_venda`...). `situacao.aprovado/cancelado` gateiam o pipeline. PII
 * (cliente/endereco_entrega) sem máscara.
 *
 * `itens[].variacao` tem chaves DINÂMICAS (nome do atributo: Cor, Sabor,
 * Tamanho...) e valor ora objeto ora string — passthrough (ver Item::$variacao).
 *
 * @property list<Item>|null $itens
 * @property list<Pagamento>|null $pagamentos
 * @property list<Envio>|null $envios
 */
final class OrderResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $id = null,
        public readonly ?int $numero = null,
        public readonly ?string $idExterno = null,
        public readonly ?string $idAnymarket = null,
        public readonly ?Situacao $situacao = null,
        public readonly ?Cliente $cliente = null,
        public readonly ?string $clienteObs = null,
        public readonly ?Endereco $enderecoEntrega = null,
        public readonly ?CupomDesconto $cupomDesconto = null,
        public readonly ?IntegrationData $integrationData = null,
        // Dinheiro: STRING.
        public readonly ?string $valorTotal = null,
        public readonly ?string $valorSubtotal = null,
        public readonly ?string $valorDesconto = null,
        public readonly ?string $valorEnvio = null,
        public readonly ?string $pesoReal = null,
        // Datas ISO-8601 (string).
        public readonly ?string $dataCriacao = null,
        public readonly ?string $dataModificacao = null,
        public readonly ?string $dataExpiracao = null,
        public readonly ?string $utmCampaign = null,
        public readonly ?string $resourceUri = null,
        #[ArrayOf(Item::class)]
        public readonly ?array $itens = null,
        #[ArrayOf(Pagamento::class)]
        public readonly ?array $pagamentos = null,
        #[ArrayOf(Envio::class)]
        public readonly ?array $envios = null,
    ) {}
}
