<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\DTO\Response\Invoice;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * NF-e de uma entrega (item de `results[]` de listForDelivery). A chave vem em
 * `accessKey` OU `key`; o corpo XML inteiro pode vir inline em `xml` (grande).
 * `xmlUrl`/`url` é o link alternativo. Dinheiro (`totalAmount`) aqui é ESCALAR
 * simples da API de invoices (não o objeto Money normalizado dos pedidos).
 */
final class DeliveryInvoice implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $accessKey = null,
        public readonly ?string $key = null,
        public readonly ?string $number = null,
        public readonly ?string $serie = null,
        public readonly ?string $series = null,
        public readonly ?string $status = null,
        public readonly mixed $totalAmount = null,
        public readonly mixed $amount = null,
        public readonly ?string $issuedAt = null,
        public readonly ?string $accessToken = null,
        public readonly ?string $xml = null,
        public readonly ?string $xmlUrl = null,
        public readonly ?string $url = null,
    ) {}
}
