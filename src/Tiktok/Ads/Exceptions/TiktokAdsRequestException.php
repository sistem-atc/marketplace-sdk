<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\Ads\Exceptions;

use Exception;

/**
 * Erro da Marketing API do TikTok for Business.
 *
 * A API responde HTTP 200 com `code != 0` no corpo — o code da API vai no
 * code da exception e a `message` da API na message.
 */
class TiktokAdsRequestException extends Exception
{
    public function __construct(string $message, int $apiCode = 0)
    {
        parent::__construct($message, $apiCode);
    }
}
