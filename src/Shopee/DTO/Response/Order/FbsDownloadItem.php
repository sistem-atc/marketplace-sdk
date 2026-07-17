<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\DTO\Response\Order;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * Link do ZIP de NFes FBS (item do `response` do download_fbs_invoices).
 *
 * `fileLink` é URL S3 ASSINADA e EXPIRA (X-Amz-Expires=1800 = 30min) —
 * baixe já, não guarde.
 */
final class FbsDownloadItem implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?int $requestId = null,
        public readonly ?string $fileLink = null,
    ) {}
}
