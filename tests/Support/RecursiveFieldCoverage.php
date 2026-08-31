<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tests\Support;

/**
 * Acha campo que o DTO DESCARTOU na desserializacao — descendo em objeto
 * aninhado e em lista, nao so' nas chaves de topo.
 *
 * A regressao que originou esta lib foi exatamente essa: `is_refundable_sample`
 * vivia dentro de `line_items[]` e sumia sem que nenhum teste reclamasse,
 * porque a comparacao so' olhava o primeiro nivel do payload.
 *
 * Devolve os caminhos pontilhados (`conversations.0.latest_message.index`) do
 * que existe no payload da API e nao sobreviveu ao roundtrip. Chave com valor
 * null no payload nao conta: o CastToArray omite nulo de proposito.
 */
final class RecursiveFieldCoverage
{
    /**
     * @param  array<mixed>  $payload  o que a API mandou
     * @param  array<mixed>  $roundtrip  DTO::fromArray($payload)->toArray()
     * @return list<string> caminhos perdidos, vazio quando nada se perdeu
     */
    public static function missing(array $payload, array $roundtrip, string $prefix = ''): array
    {
        $perdidos = [];

        foreach ($payload as $key => $value) {
            $path = $prefix === '' ? (string) $key : $prefix.'.'.$key;

            if ($value === null) {
                continue;
            }

            if (! array_key_exists($key, $roundtrip)) {
                $perdidos[] = $path;

                continue;
            }

            if (is_array($value) && is_array($roundtrip[$key])) {
                $perdidos = array_merge($perdidos, self::missing($value, $roundtrip[$key], $path));
            }
        }

        return $perdidos;
    }
}
