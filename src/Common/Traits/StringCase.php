<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Common\Traits;

/**
 * Helpers de caixa (camel/snake) compartilhados pelos traits de DTO.
 * Extraido pra evitar colisao quando um DTO usa AutoHydrate + CastToArray.
 */
trait StringCase
{
    private static function camelToSnake(string $name): string
    {
        return strtolower((string) preg_replace('/[A-Z]/', '_$0', $name));
    }
}
