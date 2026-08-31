<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\Fulfillment;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Endereco de um pacote — serve tanto `recipient_address` quanto
 * `sender_address` (a doc declara os DOIS com exatamente os mesmos campos).
 *
 * Diferente do `recipient_address` do PEDIDO, aqui NAO ha `district_info`:
 * cidade/estado so' existem soltos dentro de `fullAddress`/`addressLine*`.
 *
 * PII: quando o pacote usa logistica da plataforma, o TikTok DESSENSIBILIZA
 * `name` e `phoneNumber` ("(+1)213-***-1234"). Nao use este endereco como
 * fonte de PII pra nota — use o do pedido.
 */
final class PackageAddress implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?string $fullAddress = null,
        public readonly ?string $phoneNumber = null,
        public readonly ?string $name = null,
        public readonly ?string $postalCode = null,
        public readonly ?string $addressDetail = null,
        public readonly ?string $regionCode = null,
        // camelToSnake gera address_line1 (so' quebra em MAIUSCULA), que e'
        // exatamente a chave da API. Nao precisa de #[JsonKey].
        public readonly ?string $addressLine1 = null,
        public readonly ?string $addressLine2 = null,
        // Linhas 3 e 4 sao "usualmente so' do mercado brasileiro" (doc).
        public readonly ?string $addressLine3 = null,
        public readonly ?string $addressLine4 = null,
    ) {}
}
