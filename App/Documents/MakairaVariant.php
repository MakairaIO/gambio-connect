<?php

declare(strict_types=1);

namespace GXModules\Makaira\GambioConnect\App\Documents;

use DateTime;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\Collections\ProductVariants;
use Gambio\Admin\Modules\Product\Submodules\Variant\Model\ProductVariant;

class MakairaVariant extends MakairaEntity
{
    private array $makairaDocument = [];
    private array $product;
    private ProductVariants $variants;
    private bool $delete = false;
    private array $options = [];
    private string $now;


    public function toArray(): array
    {
        return [
             /* Makaira fields */
             ...parent::toArray(),

             /* Product fields */

        ];
    }


    private function mapProduct(): array
    {
        if (!$this->product) {
            return false;
        }

        $document = [
            'data' =>  [
                'type'                         => self::DOC_TYPE,
                'id'                           => $this->product['products_id'],
                'parent'                       => '',
                'shop'                         => 1,
                'ean'                          => $this->product['products_ean'],
                'activeto'                     => '',
                'activefrom'                   => '',
                'isVariant'                    => false,
                'active'                       => $this->getActive(),
                'sort'                         => 0,
                'stock'                        => $this->getStock(),
                'onstock'                      => $this->getStock() > 0,
                'picture_url_main'             => $this->product['products_image'],
                'title'                        => $this->product['products_name'],
                'shortdesc'                    => $this->product['products_short_description'],
                'longdesc'                     => $this->product['products_description'],
                'price'                        => $this->product['products_price'],
                'soldamount'                   => "",
                'searchable'                   => true,
                'searchkeys'                   => $this->product['products_keywords'] ?? '',
                'url'                          => $this->getUrl(),
                'maincategory'                 => $this->product['main_category_id'],
                'maincategoryurl'              => "",
                'category'                     => [],
                'attributes'                   => [],
                'mak_boost_norm_insert'        => 0.0,
                'mak_boost_norm_sold'          => 0.0,
                'mak_boost_norm_rating'        => 0.0,
                'mak_boost_norm_revenue'       => 0.0,
                'mak_boost_norm_profit_margin' => 0.0,
                'timestamp'                    => $this->now,
                'manufacturerid'               => $this->product['manufacturers_id'],
                'manufacturer_title'           => '',
            ],
            'source_revision' => 1,
            'language_id' => $this->getLanguage()
        ];
        if ($this->delete) {
            $document['delete'] = true;
        }

        return $document;
    }


    private function mapVariant(ProductVariant $variant): array
    {
        $document = [
            'data' =>  [
                'type'                         => "variant",
                'id'                           => $variant->id(),
                'parent'                       => $this->product['products_id'],
                'shop'                         => 1,
                'ean'                          => $variant->ean(),
                'activeto'                     => '',
                'activefrom'                   => '',
                'isVariant'                    => true,
                'active'                       => $this->getActive(),
                'sort'                         => 0,
                'stock'                        => $variant->stock(),
                'onstock'                      => $variant->stock() > 0,
                'picture_url_main'             => $this->product['products_image'],
                'title'                        => $this->product['products_name'],
                'shortdesc'                    => $this->product['products_short_description'],
                'longdesc'                     => $this->product['products_description'],
                'price'                        => $variant->price(),
                'soldamount'                   => "",
                'searchable'                   => true,
                'searchkeys'                   => $this->product['products_keywords'] ?? '',
                'url'                          => $this->getUrl(),
                'maincategory'                 => $this->product['main_category_id'],
                'maincategoryurl'              => "",
                'category'                     => [],
                'attributes'                   => [],
                'mak_boost_norm_insert'        => 0.0,
                'mak_boost_norm_sold'          => 0.0,
                'mak_boost_norm_rating'        => 0.0,
                'mak_boost_norm_revenue'       => 0.0,
                'mak_boost_norm_profit_margin' => 0.0,
                'timestamp'                    => $this->now,
                'manufacturerid'               => $this->product['manufacturers_id'],
                'manufacturer_title'           => '',

                // 'additionalData'               => [
                //     'releaseDate'        => (string) $releaseDate,
                //     'popularity'         => $product->getSales(),
                //     'creationDate'       => (string) $creationDate,
                //     'weight'             => $product->getWeight(),
                //     'shippingFree'       => $product->isShippingFree(),
                //     'highlight'          => $product->highlight(),
                //     'width'              => $product->getWidth(),
                //     'height'             => $product->getHeight(),
                //     'length'             => $product->getLength(),
                // ],
            ],
            'source_revision' => 1,
            'language_id' => $this->getLanguage()
        ];

        if ($this->delete) {
            $document['delete'] = true;
        }

        return $document;
    }


    public function addMakairaDocumentWrapper(): array
    {
        return [
            'items' => $this->makairaDocument,
            'import_timestamp' =>  $this->now,
            'source_identifier' => 'gambio'
        ];
    }

    private function getActive(): bool
    {
        return true;
    }

    private function getLanguage(): string
    {
        return 'de';
    }

    private function getUrl(): string
    {
        return "";
    }

    private function getStock(): int
    {
        return 1;
    }



    private function getCategories(): array
    {
        return [];
    }


    private function checkQualityGate(): bool
    {

        return true;


        // check if all required fields are set
        // for errors

    }
}
