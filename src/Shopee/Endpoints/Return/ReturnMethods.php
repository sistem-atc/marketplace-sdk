<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Shopee\Endpoints\Return;

use SistemAtc\Marketplaces\Shopee\Bases\BaseMethods;
use SistemAtc\Marketplaces\Common\Enums\HttpMethod;

class ReturnMethods extends BaseMethods
{
    public function getReturnList(int $pageNo = 0, int $pageSize = 20): array
    {
        return $this->makeRequest(HttpMethod::GET, '/api/v2/returns/get_return_list', [
            'page_no' => $pageNo,
            'page_size' => $pageSize,
        ]);
    }

    public function getReturnDetail(string $returnSn): array
    {
        return $this->makeRequest(HttpMethod::GET, '/api/v2/returns/get_return_detail', [
            'return_sn' => $returnSn,
        ]);
    }

    public function confirmReturn(string $returnSn): array
    {
        return $this->makeRequest(HttpMethod::POST, '/api/v2/returns/confirm', [], [
            'return_sn' => $returnSn,
        ]);
    }
}
