<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\ConectaLa\Exceptions;

use Exception;
use Illuminate\Http\Client\Response;

class ConectaLaRequestException extends Exception
{
    public function __construct(protected Response $response)
    {
        $body = $response->json() ?? [];
        $msg = $body['message']
            ?? $body['error']
            ?? $body['erro']
            ?? $body['detail']
            ?? 'Erro ao consumir a API da Conecta Lá.';

        parent::__construct(
            message: is_string($msg) ? $msg : json_encode($msg),
            code: $response->status(),
        );
    }

    public function status(): int
    {
        return $this->response->status();
    }

    public function response(): Response
    {
        return $this->response;
    }

    /** Corpo de erro cru (a API varia entre message/error/erro). */
    public function body(): array
    {
        return $this->response->json() ?? [];
    }

    public function url(): ?string
    {
        try {
            return $this->response->effectiveUri()?->__toString();
        } catch (\Throwable) {
            return null;
        }
    }
}
