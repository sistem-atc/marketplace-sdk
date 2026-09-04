<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopify\Exceptions;

use Exception;

/**
 * Erro devolvido no array `errors` da resposta GraphQL (sintaxe, campo
 * inexistente, acesso negado, throttle esgotado...). `userErrors` de
 * mutations NAO viram excecao — voltam dentro do payload.
 */
class ShopifyGraphQLException extends Exception
{
    /**
     * @param array<int,array<string,mixed>> $errors
     * @param array<string,mixed> $extensions
     */
    public function __construct(
        protected array $errors,
        protected array $extensions = [],
        int $status = 200,
    ) {
        $messages = array_map(fn (array $e) => (string) ($e['message'] ?? 'unknown'), $errors);
        parent::__construct('Shopify GraphQL: '.implode(' | ', $messages), $status);
    }

    /** @return array<int,array<string,mixed>> */
    public function errors(): array
    {
        return $this->errors;
    }

    /** @return array<string,mixed> */
    public function extensions(): array
    {
        return $this->extensions;
    }

    public function isThrottled(): bool
    {
        foreach ($this->errors as $e) {
            if (($e['extensions']['code'] ?? null) === 'THROTTLED') {
                return true;
            }
        }

        return false;
    }
}
