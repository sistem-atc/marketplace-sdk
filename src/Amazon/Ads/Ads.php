<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces\Amazon\Ads;

use SistemAtc\Marketplaces\Amazon\Ads\Endpoints\Account\AccountMethods;
use SistemAtc\Marketplaces\Amazon\Ads\Endpoints\Audiences\AudiencesMethods;
use SistemAtc\Marketplaces\Amazon\Ads\Endpoints\Creatives\CreativeAssetsMethods;
use SistemAtc\Marketplaces\Amazon\Ads\Endpoints\Dsp\DspMeasurementMethods;
use SistemAtc\Marketplaces\Amazon\Ads\Endpoints\Dsp\DspMethods;
use SistemAtc\Marketplaces\Amazon\Ads\Endpoints\Insights\InsightsMethods;
use SistemAtc\Marketplaces\Amazon\Ads\Endpoints\Reporting\ReportingMethods;
use SistemAtc\Marketplaces\Amazon\Ads\Endpoints\SponsoredBrands\SponsoredBrandsMethods;
use SistemAtc\Marketplaces\Amazon\Ads\Endpoints\SponsoredBrands\SponsoredBrandsV3Methods;
use SistemAtc\Marketplaces\Amazon\Ads\Endpoints\SponsoredDisplay\SponsoredDisplayMethods;
use SistemAtc\Marketplaces\Amazon\Ads\Endpoints\SponsoredProducts\SponsoredProductsMethods;
use SistemAtc\Marketplaces\Amazon\Ads\Endpoints\SponsoredProducts\SponsoredProductsV2Methods;
use SistemAtc\Marketplaces\Contracts\MarketplaceIntegration;

/**
 * Hub da Amazon Ads API (advertising-api.amazon.com). A integration e' a de
 * ADS (LwA proprio, escopo advertising::campaign_management), nao a da SP-API;
 * $clientId e' o client do Security Profile de ads.
 */
class Ads
{
    public function __construct(
        private readonly MarketplaceIntegration $integration,
        private readonly string $clientId,
    ) {}

    public function reporting(): ReportingMethods { return new ReportingMethods($this->integration, $this->clientId); }
    public function account(): AccountMethods { return new AccountMethods($this->integration, $this->clientId); }
    public function sponsoredProducts(): SponsoredProductsMethods { return new SponsoredProductsMethods($this->integration, $this->clientId); }
    /** @deprecated v2 legado */
    public function sponsoredProductsV2(): SponsoredProductsV2Methods { return new SponsoredProductsV2Methods($this->integration, $this->clientId); }
    public function sponsoredBrands(): SponsoredBrandsMethods { return new SponsoredBrandsMethods($this->integration, $this->clientId); }
    public function sponsoredBrandsV3(): SponsoredBrandsV3Methods { return new SponsoredBrandsV3Methods($this->integration, $this->clientId); }
    public function sponsoredDisplay(): SponsoredDisplayMethods { return new SponsoredDisplayMethods($this->integration, $this->clientId); }
    public function dsp(): DspMethods { return new DspMethods($this->integration, $this->clientId); }
    public function dspMeasurement(): DspMeasurementMethods { return new DspMeasurementMethods($this->integration, $this->clientId); }
    public function audiences(): AudiencesMethods { return new AudiencesMethods($this->integration, $this->clientId); }
    public function insights(): InsightsMethods { return new InsightsMethods($this->integration, $this->clientId); }
    public function creativeAssets(): CreativeAssetsMethods { return new CreativeAssetsMethods($this->integration, $this->clientId); }
}
