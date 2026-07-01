<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Netshoes\Exceptions;

use Exception;
use Illuminate\Http\Client\Response;

/**
 * Erro de request da API Netshoes (Sensedia gateway). A API responde erros
 * em formatos variados — extraimos a melhor mensagem disponivel.
 *
 * Codigos observados em testes reais:
 *   - 403 "Access Denied"      -> token sem escopo de Orders
 *   - 409 "migracao para API V2" -> uso de V1 (descontinuada)
 *   - 404 em /oauth/*           -> NAO ha exchange OAuth (auth e' 2 headers)
 */
class NetshoesRequestException extends Exception
{
    public function __construct(protected Response $response)
    {
        $body = $response->json() ?? [];
        $msg = $body['message']
            ?? $body['error_description']
            ?? $body['error']
            ?? $body['detail']
            ?? (is_string($response->body()) && $response->body() !== '' ? $response->body() : null)
            ?? 'Erro ao consumir a API da Netshoes.';

        parent::__construct(
            message: is_string($msg) ? $msg : json_encode($msg),
            code: $response->status(),
        );
    }

    public function status(): int
    {
        return $this->response->status();
    }

    public function url(): ?string
    {
        try {
            return $this->response->effectiveUri()?->__toString();
        } catch (\Throwable) {
            return null;
        }
    }

    /** @return array<string, mixed> */
    public function responseJson(): array
    {
        try {
            return $this->response->json() ?? [];
        } catch (\Throwable) {
            return [];
        }
    }

    public function response(): Response
    {
        return $this->response;
    }
}
