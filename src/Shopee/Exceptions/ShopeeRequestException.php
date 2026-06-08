<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\Exceptions;

use Exception;
use Illuminate\Http\Client\Response;

class ShopeeRequestException extends Exception
{
    public function __construct(protected Response $response)
    {
        parent::__construct($response->body(), $response->status());
    }

    public function status(): int
    {
        return $this->response->status();
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

    /** Codigo de erro Shopee (ex: 'order_not_found', 'error_auth', ...). */
    public function shopeeError(): ?string
    {
        $err = $this->responseJson()['error'] ?? null;

        return is_string($err) && $err !== '' ? $err : null;
    }

    public function shopeeMessage(): ?string
    {
        $msg = $this->responseJson()['message'] ?? null;

        return is_string($msg) && $msg !== '' ? $msg : null;
    }

    public function response(): Response
    {
        return $this->response;
    }
}
