<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\LojaIntegrada\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;
use SistemAtc\Marketplaces\LojaIntegrada\DTO\Response\Order\FormaPagamento;
use SistemAtc\Marketplaces\LojaIntegrada\DTO\Response\Order\Parcelamento;

/** Pagamento (pagamentos[]). Campos sempre-nulos (banco, authorization_code...) omitidos. */
final class Pagamento implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $id = null,
        // Dinheiro e STRING no LojaIntegrada.
        public readonly ?string $valor = null,
        public readonly ?string $valorPago = null,
        public readonly ?string $bandeira = null,
        public readonly ?string $boletoUrl = null,
        public readonly ?string $pagamentoTipo = null,
        public readonly ?string $transacaoId = null,
        public readonly ?string $pixCode = null,
        public readonly ?string $pixQrcode = null,
        public readonly ?Parcelamento $parcelamento = null,
        public readonly ?FormaPagamento $formaPagamento = null,
    ) {}
}
