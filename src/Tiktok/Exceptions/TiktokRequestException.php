<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\Exceptions;

use Exception;
use Illuminate\Http\Client\Response;

class TiktokRequestException extends Exception
{
    public function __construct(protected Response $response)
    {
        parent::__construct($response->body(), $response->status());
    }

    public function status(): int
    {
        return $this->response->status();
    }
}
