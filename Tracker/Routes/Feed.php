<?php
/**
 * @copyright   Copyright (c) 2023 TheMarketer.com
 * @project     TheMarketer.com
 * @website     https://themarketer.com/
 * @author      Alexandru Buzica (EAX LEX S.R.L.) <b.alex@eax.ro>
 * @license     https://opensource.org/licenses/osl-3.0.php - Open Software License (OSL 3.0)
 * @docs        https://themarketer.com/resources/api
 */

namespace Mktr\Tracker\Routes;

use Mktr\Tracker\Model\Product;
use Mktr\Tracker\Valid;

class Feed
{
    private static $init = null;

    private static $map = array(
        "fileName" => "products",
        "secondName" => "product"
    );

    public static function init()
    {
        if (self::$init == null) {
            self::$init = new self();
        }
        return self::$init;
    }

    public static function get($f = 'fileName'){
        if (isset(self::$map[$f]))
        {
            return self::$map[$f];
        }
        return null;
    }

    public static function execute()
    {
        if (Product::cOverWrite()) {
            $productTypes = array( 'simple', 'grouped', 'external', 'variable', 'woosb' );
        } else {
            $productTypes = apply_filters('marketer_override_feed_product_types', ['simple', 'grouped', 'external', 'variable', 'woosb']);
        }
        
        $args = array(
            'status' => array(
                /* 'pending', */
                'publish'
            ),
            'type' => $productTypes,
            'order'   => 'ASC',
            'orderby' => 'ID',
            'return' => 'ids',
            'limit'   => Valid::getParam('limit', 200),
            'paginate' => true,
            'paged' => 1,
        );

        $stop = false;
        $page = Valid::getParam('page', null);

        if ($page !== null) {
            $stop = true;
        }

        $args['paged'] = $page === null ? 1 : $page;

        $get = array();
        $toSkip = array();

        do {
            $products = wc_get_products($args);
            $pages = $stop ? 0 : $products->max_num_pages;

            foreach ($products->products as $val)
            {
                Product::getById($val);
                
                if (Product::cOverWrite()) {
                    if (Product::getRegularPrice() <= 0 && Product::getPrice() <= 0 || Product::getImage() == false) {
                        continue;
                    }
                } else {
                    if (Product::getImage() == false) {
                        continue;
                    }
                }

                $created_at = Product::getCreatedAt();

                if ($created_at === null) {
                    $created_at = '2000-01-01 13:00';
                } else {
                    $created_at = Valid::correctDate($created_at);
                }

                $variation = Product::getVariation();

                $oo = array(
                    'id' => Product::getId(),
                    'sku' => Product::getSku(),
                    'name' => ['@cdata' => Product::getName()],
                    'description' => ['@cdata' => Product::getDescription()],
                    'url' => Product::getUrl(),
                    'main_image' => Product::getImage(),
                    'category' => [ '@cdata' => Product::getCat() ],
                    'brand' => ['@cdata' => Product::getBrand()],
                    'acquisition_price' => Product::getAcquisitionPrice(),
                    'price' => Valid::digit2(Product::getRegularPrice()),
                    'sale_price' => Valid::digit2(Product::getPrice()),
                    'sale_price_start_date' => Valid::correctDate(Product::getSpecialFromDate()),
                    'sale_price_end_date' => Valid::correctDate(Product::getSpecialToDate()),
                    'availability' => Product::getAvailability(),
                    'stock' => Product::getStock(),
                    'media_gallery' => Product::getImages(),
                    'variations' => array(
                        'variation' => $variation
                    ),
                    'created_at' => $created_at,
                );
                
                if (empty($oo['media_gallery']['image'])) {
                    $oo['media_gallery']['image'][] = $oo['main_image'];
                }

                foreach ($oo as $key =>$val1) {
                    if ($key == 'variations') {
                        if (empty($val1['variation'])) {
                            unset($oo[$key]);
                        }
                    } else {
                        if (empty($val1) && $val1 != 0 || $val1 === null) {
                            unset($oo[$key]);
                        }
                    }
                }
                $get[] = $oo;

            }
            $args['paged']++;

        } while ($args['paged'] <= $pages);

        return $get;
    }
}