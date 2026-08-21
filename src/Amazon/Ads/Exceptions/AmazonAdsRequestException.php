<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Ads\Exceptions;

use Exception;

/**
 * Erro da Amazon Ads API (advertising-api.amazon.com) — NÃO confundir com a
 * SP-API. O status HTTP vai no code; a mensagem vem do corpo (`message` ou
 * `details`) quando existe.
 */
class AmazonAdsRequestException extends Exception
{
    public function __construct(string $message, int $status = 0)
    {
        parent::__construct($message, $status);
    }
}
