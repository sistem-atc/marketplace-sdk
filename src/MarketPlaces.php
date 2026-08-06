<?php

declare(strict_types=1);

namespace SistemAtc\Marketplaces;

use SistemAtc\Marketplaces\MercadoLivre\MercadoLivre;
use SistemAtc\Marketplaces\Shopee\Shopee;
use SistemAtc\Marketplaces\Magalu\Magalu;
use SistemAtc\Marketplaces\Tiktok\Tiktok;
use SistemAtc\Marketplaces\Amazon\Amazon;
use SistemAtc\Marketplaces\Shopify\Shopify;
use SistemAtc\Marketplaces\MercadoPago\MercadoPago;
use SistemAtc\Marketplaces\LojaIntegrada\LojaIntegrada;
use SistemAtc\Marketplaces\Netshoes\Netshoes;
use SistemAtc\Marketplaces\ConectaLa\ConectaLa;

class MarketPlaces
{
    public static function MercadoLivre(): MercadoLivre
    {
        return new MercadoLivre();
    }

    public static function ConectaLa(): ConectaLa
    {
        return new ConectaLa();
    }

    public static function Shopee(): Shopee
    {
        return new Shopee();
    }

    public static function Magalu(): Magalu
    {
        return new Magalu();
    }

    public static function Tiktok(): Tiktok
    {
        return new Tiktok();
    }

    public static function Amazon(): Amazon
    {
        return new Amazon();
    }

    public static function Shopify(): Shopify
    {
        return new Shopify();
    }

    public static function MercadoPago(): MercadoPago
    {
        return new MercadoPago();
    }

    public static function LojaIntegrada(): LojaIntegrada
    {
        return new LojaIntegrada();
    }

    public static function Netshoes(): Netshoes
    {
        return new Netshoes();
    }

}
