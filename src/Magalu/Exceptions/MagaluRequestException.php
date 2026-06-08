<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Magalu\Exceptions;

use Exception;
use Illuminate\Http\Client\Response;

class MagaluRequestException extends Exception
{
    public function __construct(protected Response $response)
    {
        $body = $response->json() ?? [];
        $msg = $body['message']
            ?? $body['detail']
            ?? $body['error_description']
            ?? $body['error']
            ?? 'Erro ao consumir a API da Magalu.';

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

    public function magaluError(): ?string
    {
        $j = $this->responseJson();

        return $j['error'] ?? $j['detail'] ?? null;
    }

    public function magaluMessage(): ?string
    {
        $j = $this->responseJson();

        return $j['message'] ?? $j['error_description'] ?? null;
    }

    public function response(): Response
    {
        return $this->response;
    }
}
