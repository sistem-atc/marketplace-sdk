<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Tiktok\DTO\Response\AffiliateCreator;

use SistemAtc\Marketplaces\Common\Traits\AutoHydrate;
use SistemAtc\Marketplaces\Common\Traits\CastToArray;
use SistemAtc\Marketplaces\Contracts\DTOInterface;

/**
 * `data` de POST /affiliate_creator/202511/videos/precheck_task.
 *
 * Assincrono: devolve o task_id na hora; o veredicto sai no
 * getShoppableVideoPrecheckResult().
 */
final class VideoPrecheckTaskResponseDTO implements DTOInterface
{
    use AutoHydrate;
    use CastToArray;

    public function __construct(
        public readonly ?VideoPrecheckCreated $precheck = null,
    ) {}
}
