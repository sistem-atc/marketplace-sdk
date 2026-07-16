<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Contracts;

/**
 * Contrato dos DTOs de retorno das APIs do pacote.
 *
 * Todo retorno de chamada de API que hoje devolve `array` mapeado a mao deve
 * migrar pra um DTO que implemente esta interface — UM unico ponto de parse
 * (`fromArray`), tipos solidos, sem adivinhar shape de resposta no consumidor.
 *
 * Espelha o padrao do pacote sistem-atc/asaas-laravel.
 */
interface DTOInterface
{
    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self;

    /** @return array<string, mixed> */
    public function toArray(): array;
}
