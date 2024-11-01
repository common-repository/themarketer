<?php
/**
 * @copyright   Copyright (c) 2023 TheMarketer.com
 * @project     TheMarketer.com
 * @website     https://themarketer.com/
 * @author      Alexandru Buzica (EAX LEX S.R.L.) <b.alex@eax.ro>
 * @license     https://opensource.org/licenses/osl-3.0.php - Open Software License (OSL 3.0)
 * @docs        https://themarketer.com/resources/api
 */

namespace Mktr\Tracker\Model;

use Mktr\Tracker\Config;
use Mktr\Tracker\Events;

/**
 * @method static getId()
 * @method static getName()
 * @method static getParentId()
 * @method static getSku()
 * @method static getAvailableVariations()
 * @method static getUrl()
 * @method static getImg()
 * @method static getStockQuantity()
 * @method static getIsInStock()
 * @method static getMainImgId()
 * @method static getGalleryImageIds()
 * @method static getSalePrice()
 * @method static getSaleRegularPrice()
 * @method static getDescription()
 * @method static getSpecialFromDate()
 * @method static getSpecialToDate()
 * @method static getCreatedAt()
 * @method static getModifiedAt()
 */
class Product
{
    private static $init = null;
    private static $asset = null;
    private static $data = array();
    private static $tax = null;
    private static $stock = 0;
    private static $nameConvert = null;

    private static $valueNames = array(
        'getId' => 'get_id',
        // 'getName' => 'get_name',
        'getParentId' => 'get_parent_id',
        'getSku' => 'get_sku',
        // 'getAvailableVariations' => 'get_available_variations',
        'getUrl' => 'get_permalink',
        'getImg' => 'get_image',
        'getStockQuantity' => 'get_stock_quantity',
        'getIsInStock' => 'is_in_stock',
        'getMainImgId' =>'get_image_id',
        'getGalleryImageIds' => 'get_gallery_image_ids',
        'getSalePrice'=>'get_sale_price',
        'getSaleRegularPrice' => 'get_regular_price',
        // 'getDescription' => 'get_description',
        'getSpecialFromDate' =>'get_date_on_sale_from',
        'getSpecialToDate' => 'get_date_on_sale_to',
        'getCreatedAt' =>'get_date_created',
        'getModifiedAt' => 'get_date_modified'
    );

    private static $varNames = array(
        'getId' => 'get_id',
        'getName' => 'get_name',
        'getParentId' => 'get_parent_id',
        'getSku' => 'get_sku'
    );

    private static $selfValue = array(
        "product_id" => "getId",
        "name" => "getName",
        // "parent_id" => "getParentId",
        "sku" => "getSku",
        "variation" => "getVariation"
    );
    private static $AcquisitionPriceMeta = null;

    public static $getOverWrite = null;

    public static function init()
    {
        if (self::$init == null) {
            self::$init = new self();
        }
        return self::$init;
    }

    public static function checkTax()
    {
        if (self::$tax === null){ self::$tax = wc_tax_enabled(); }
        return self::$tax;
    }

    public static function __callStatic($name, $arguments)
    {
        return self::getValue($name);
    }

    public function __call($name, $arguments)
    {
        return self::getValue($name);
    }

    public static function getValue($name)
    {
		if (is_bool(self::$asset)) {
            return null;
        }

        if (self::$asset == null || self::$asset === false) {
            self::getById();
        }

        if (isset(self::$data[$name])) {
            return self::$data[$name];
        }
        
        if ((self::$asset !== null && self::$asset !== false ) && isset(self::$valueNames[$name])) {
            $v = self::$valueNames[$name];
            self::$data[$name] = self::$asset->{$v}();
            
            if ($name === "getSku" ) {
                self::$data[$name] = 
                    empty(self::$data[$name]) ?
                        self::getId() : self::$data[$name];
            }

            return self::$data[$name];
        }
        return null;
    }

    public static function getVarValue($name, $var = null)
    {
        if (self::$asset == null) {
            self::getById();
        }

        if (isset(self::$data[$name])) {
            return self::$data[$name];
        }

        if (isset(self::$varNames[$name])) {
            $v = self::$varNames[$name];
            self::$data[$name] = $var->{$v}();
            return self::$data[$name];
        }
        return null;
    }

    public static function getById($id = null)
    {
        if ($id == null) { $id = get_the_ID(); }
        self::$data = array();
        self::$asset = wc_get_product($id);
        self::$stock = 0;
		if (is_bool(self::$asset)) { return false; } 
        return self::init();
    }

    public static function getCreate()
    {
        return self::getCreatedAt() === null ? self::getModifiedAt() : self::getCreatedAt();
    }

    public static function getAcquisitionPrice()
    {
        $id = self::getId();

        $margin = 0;

        if (self::$AcquisitionPriceMeta === null)
        {
            $margin = get_post_meta($id, '_wc_cog_cost' );

            if (empty($margin))
            {
                $margin = get_post_meta($id, '_alg_wc_cog_cost' );
            } else {
                self::$AcquisitionPriceMeta = '_wc_cog_cost';
            }

            if(!empty($margin)) {
                self::$AcquisitionPriceMeta = '_alg_wc_cog_cost';
            } else {
                $margin = 0;
            }
        } else if (self::$AcquisitionPriceMeta !== null && self::$AcquisitionPriceMeta !== false) {
            $margin = get_post_meta($id, self::$AcquisitionPriceMeta);
        }

        if (empty($margin))
        {
            $margin = 0;
        }

        return $margin;
    }

    public static function getCat()
    {
        return Events::buildMultiCategory(get_the_terms(self::getId(), \Mktr\Tracker\Config::getProductCat()));
    }

    public static function qTranslate($string) {
        $split_regex = "#(<!--:[a-z]{2}-->|<!--:-->|\[:[a-z]{2}\]|\[:\]|\{:[a-z]{2}\}|\{:\})#ism";
        $matches = preg_split($split_regex, $string, -1, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);

        if (isset($matches[1])) {
            return $matches[1];
        }

        return $string;
    }

    private static function nameConvert() {
        if (self::$nameConvert === null) { self::$nameConvert = function_exists( 'qtranxf_split' ); }
        return self::$nameConvert;
    }

    public static function getName() {
        $name = (self::nameConvert() ? self::qTranslate(self::getVarValue('getName', self::$asset), ) : self::getVarValue('getName', self::$asset));
        $nameFilter = apply_filters( 'woocommerce_product_title', $name, self::$asset );
        if (empty($nameFilter)) {
            return $name;
        } else {
            return $nameFilter;
        }
    }
    
    public static function getDescription() {
        if (Config::getAddDescription() === 0) {
            return self::getVarValue('getName', self::$asset);
        }
        
        if (defined('ICL_LANGUAGE_CODE')) {
            if (ICL_LANGUAGE_CODE == 'en') {
                //var_dump(ICL_LANGUAGE_CODE); die();
                $en_content = get_post_meta(self::getId(), 'product_english_description', true);
                if (!empty($en_content)) {
                    return $en_content;
                }
            }
        }
        
        return self::nameConvert() ? self::qTranslate(self::getValue('getDescription')) : self::getValue('getDescription');
    }

    public static function getBrand()
    {
        if (empty(self::$asset)) {
            return "N/A";
        }
        $b = '';
        foreach (Config::getBrandAttribute() as $v)
        {
            $b = self::$asset->get_attribute($v);
            if (empty($b))
            {
                $b = self::$asset->get_attribute('pa_'.$v);
                if (!empty($b))
                {
                    break;
                }
            } else {
                break;
            }
        }
        return empty($b) ? "N/A" : $b;
    }

    public static function getPriceByPriority($price1 = 0, $price2 = 0) {
        if ($price1 > 0) {
            return $price1;
        } else if ($price2 > 0){
            return $price2;
        } else {
            return null;
        }
    }

    public static function get_price( $product, $sett = false ) {
        if ( $sett ) {
            if (!array_key_exists('get_regular_price', self::$data)) {
                self::$data['get_regular_price'] = 0;
                $children = self::$asset->get_items();
                $ids = array();
                if (MKTR_LEMS) {
                    $excludeIDS = get_post_meta(self::getId(), 'lems__exclude_ids_from_price');
                    if (isset($excludeIDS[0])) {
                        $ids = explode(',',$excludeIDS[0]);
                    }
                }
                foreach ($children as $key => $value) {
                    if (!in_array($value['id'], $ids)) {
                        $_product = wc_get_product( $value['id'] );
                        if ($_product) {
                            $pPrice = self::getPriceByPriority($_product->get_regular_price(), $_product->get_price());
                            if ($pPrice !== null) {
								if (isset($value['qty'])) {
                                	self::$data['get_regular_price'] += $pPrice * $value['qty'];
								} else {
									self::$data['get_regular_price'] += $pPrice;
								}
                            }
                        }
                    }
                }
            }
            return self::$data['get_regular_price'];
        } else {
            if (!array_key_exists('get_price', self::$data)) {
                self::$data['get_price'] = 0;
                $children = self::$asset->get_items();
                $ids = array();
                if (MKTR_LEMS) {
                    $excludeIDS = get_post_meta(self::getId(), 'lems__exclude_ids_from_price');
                    if (isset($excludeIDS[0])) {
                        $ids = explode(',',$excludeIDS[0]);
                    }
                }
                foreach ($children as $key => $value) {
                    if (!in_array($value['id'], $ids)) {
                        $_product = wc_get_product( $value['id'] );
                        if ($_product) {
                            $pPrice = self::getPriceByPriority($_product->get_price(), $_product->get_regular_price());
                            if ($pPrice !== null) {
								if (isset($value['qty'])) {
                                	self::$data['get_price'] += $pPrice * $value['qty'];
								} else {
									self::$data['get_price'] += $pPrice;
								}
                            }
                        }
                    }
                }
            }
            return self::$data['get_price'];
        }
    }
    
    public static function cOverWrite() {
        if (self::$getOverWrite === null) {
            self::$getOverWrite = \Mktr\Tracker\Config::getProductCat() === 'product_cat';
        }
        return self::$getOverWrite;
    }

    public static function getPrice($check = false) {
        if (empty(self::$asset)) {
            return 0;
        }
        $p = 0;
        $tax = false;
        if (self::$asset->is_type('variable')) {
            $v = self::getAvailableVariations();
            $def = self::$asset->get_default_attributes('none');
            
            foreach ($v as $val)
            {
                if (empty($def)) {
                    if ($p > $val['display_price'] || $p == 0 && $val['display_price'] != 0) {
                        $p = $val['display_price'];
                        break;
                    }
                } else {
                    $is_def = true;
                    foreach($def as $k => $v) {
                        if($val['attributes']['attribute_'.$k]!=$v){
                            $is_def = false;             
                        }
                    }
                    if ($is_def) {
                        $p = $val['display_price'];
                        break;
                    }
                }
            }
        } else if (self::$asset->is_type('grouped')) {
			$children = self::$asset->get_children();
            $ids = array();
            if (MKTR_LEMS) {
                $excludeIDS = get_post_meta(self::getId(), 'lems__exclude_ids_from_price');
                if (isset($excludeIDS[0])) {
                    $ids = explode(',',$excludeIDS[0]);
                }
            }
			foreach ($children as $key => $value) {
                if (!in_array($value, $ids)) {
                    $_product = wc_get_product( $value );
                    if ($_product) {
                        $pPrice = self::getPriceByPriority($_product->get_price(), $_product->get_regular_price());
                        if ($pPrice !== null) {
                            $p = $pPrice;
                            $tax = true;
                            break;
                        }
                    }
                }
			}
        } else if (self::$asset->is_type('woosb')) {
            $p = self::get_price( self::$asset );
            $tax = true;
		} else {
            $p = self::getSalePrice();
            $tax = true;
        }

        if ($tax) {
            if (self::checkTax()) {
                $p = wc_get_price_including_tax(self::$asset, array('price' => $p));
            }
        }
        if (self::cOverWrite()) {
            return \Mktr\Tracker\Valid::digit2(($check === true || $p > 0 ? $p : self::getRegularPrice(true)), 2);
        } else {
            return apply_filters('marketer_override_product_price', \Mktr\Tracker\Valid::digit2(($check === true || $p > 0 ? $p : self::getRegularPrice(true)), 2));
        }
    }

    public static function getRegularPrice($check = false)
    {
        if (empty(self::$asset)) {
            return 0;
        }
        $p = 0;
        $tax = false;
        if (self::$asset->is_type('variable')) {
            $v = self::getAvailableVariations();
            $def = self::$asset->get_default_attributes('none');

            foreach ($v as $val)
            {
                if (empty($def)) {
                    if ($p < $val['display_regular_price']) {
                        $p = $val['display_regular_price'];
                        break;
                    }
                } else {
                    $is_def = true;
                    foreach($def as $k => $v) {
                        if($val['attributes']['attribute_'.$k]!=$v){
                            $is_def=false;             
                        }
                    }
                    if ($is_def) {
                        $p = $val['display_regular_price'];
                        break;
                    }
                }
            }
        } else if (self::$asset->is_type('grouped')) {
			$children = self::$asset->get_children();
            $ids = array();
            if (MKTR_LEMS) {
                $excludeIDS = get_post_meta(self::getId(), 'lems__exclude_ids_from_price');
                if (isset($excludeIDS[0])) {
                    $ids = explode(',',$excludeIDS[0]);
                }
            }
			foreach ($children as $key => $value) {
                if (!in_array($value, $ids)) {
                    $_product = wc_get_product( $value );
                    if ($_product) {
                        $pPrice = self::getPriceByPriority($_product->get_regular_price(), $_product->get_price());
                        if ($pPrice !== null) {
                            $p = $pPrice;
                            $tax = true;
                            break;
                        }
                    }
                }
			}
		} else if (self::$asset->is_type('woosb')) {
            $p = self::get_price( self::$asset, true);
            $tax = true;
		} else {
            $p = self::getSaleRegularPrice();
            $tax = true;
        }

        if ($tax) {
            if (self::checkTax()) {
                $p = wc_get_price_including_tax(self::$asset, array('price' => $p));
            }
        }

        if (self::cOverWrite()) {
            return \Mktr\Tracker\Valid::digit2(($check === true || $p > 0 ? $p : self::getPrice(true)), 2);
        } else {
            return apply_filters('marketer_override_product_regular_price', \Mktr\Tracker\Valid::digit2(($check === true || $p > 0 ? $p : self::getPrice(true)), 2));
        }
    }

    public static function getImage()
    {
        if (self::cOverWrite()) {
            return wp_get_attachment_url(self::getMainImgId());
        } else {
            return apply_filters('marketer_override_product_image_feed', wp_get_attachment_url(self::getMainImgId()), self::$asset);
        }
    }

    public static function getImages()
    {
        $list = array(
            'image' => array()
        );

        foreach (self::getGalleryImageIds() as $id)
        {
            $list['image'][] = wp_get_attachment_url($id);
        }

        return $list;
    }
    public static function getStock() {
        if (self::$asset->is_type('grouped')) {
			$children = self::$asset->get_children();
            foreach ($children as $key => $value) {
                $_product = wc_get_product( $value );
				if ($_product) {
                    $pPrice = self::getPriceByPriority($_product->get_regular_price(), $_product->get_price());
                    if ($pPrice !== null) {
                        $MasterQty = $_product->get_stock_quantity();
                        break;
                    }
				}
			}
        } else if (self::$asset->is_type('woosb')) {
            $MasterQty = 0;
            $children = self::$asset->get_items();
            foreach ($children as $key => $value) {
                $_product = wc_get_product( $value['id'] );
                if ($_product && $_product->get_stock_quantity() > 0) {
                    $MasterQty = $MasterQty + $_product->get_stock_quantity();
                }
            }
        } else {
            $MasterQty = self::getStockQuantity();
        }
        
        if ($MasterQty < 0 || $MasterQty === null) {
            $stock = Config::getDefaultStock();
        } else {
            $stock = $MasterQty;
        }

        $stock = $stock + self::$stock;
        
        return $stock;
    }

    public static function getAvailability()
    {
        return self::checkAvailability(self::getStock(), self::getIsInStock());
    }

    public static function checkAvailability($stock = null, $status = null)
    {
        $is = 0;
        if ($stock < 0) {
            $is = Config::getDefaultStock();
        } else if ($status && ($stock === null || $stock == 0)) {
            $is = 2;
        } else if ($status || $stock > 0){
            $is = 1;
        } else {
            $is = 0;
        }

        return $is;
    }


    public static function getAvailableVariations()
    {
/* 
        return self::$asset->get_available_variations();
        self::$asset->get_available_variations();
        $var = [
            'variation_id' => $variation->get_id(),
            'sku' => $variation->get_sku(),
            'variation_is_visible' => $variation->variation_is_visible(),
            'attributes' => $variation->get_variation_attributes(),
            'display_price'         => wc_get_price_to_display( $variation ),
            'display_regular_price' => wc_get_price_to_display( $variation, array( 'price' => $variation->get_regular_price() ) ),
        ];
*/
        $variation_ids        = self::$asset->get_children();
		$available_variations = array();
        
		foreach ( $variation_ids as $variation_id ) {
            $variation = wc_get_product( $variation_id );
			if (! $variation && (! $variation->exists() || ! $variation->variation_is_visible())) {
                // || ! $variation->is_in_stock()
				continue;
			}

            $available_variations[] = self::$asset->get_available_variation( $variation );
        }
        return $available_variations;
    }
    
    public static function getVariation()
    {
        $lis = array();

        if (self::$asset->is_type('variable'))
        {
            $variable = self::getAvailableVariations();
            foreach ($variable as $val)
            {
                if ($val['variation_is_visible']) {
                    if ($val['display_regular_price'] == 0 && $val['display_price'] == 0) { continue; }
                    $attribute = [
                        'color' => null,
                        'size' => null
                    ];

                    foreach (Config::getColorAttribute() as $v)
                    {
                        if (isset($val['attributes']['attribute_'.$v]))
                        {
                            $attribute['color'] = $val['attributes']['attribute_'.$v];
                            break;
                        } else if (isset($val['attributes']['attribute_pa_'.$v]))
                        {
                            $attribute['color'] = $val['attributes']['attribute_pa_'.$v];
                            break;
                        }
                    }

                    foreach (Config::getSizeAttribute() as $v)
                    {
                        if (isset($val['attributes']['attribute_'.$v]))
                        {
                            $attribute['size'] = $val['attributes']['attribute_'.$v];
                            break;
                        } else if (isset($val['attributes']['attribute_pa_'.$v]))
                        {
                            $attribute['size'] = $val['attributes']['attribute_pa_'.$v];
                            break;
                        }
                    }

                    $MasterQty = array_key_exists('stock_quantity', $val) ? $val['stock_quantity'] : null;

                    if ($MasterQty === null){
                        $MasterQty = array_key_exists('max_qty', $val) && $val['max_qty'] !== '' ? $val['max_qty'] : null;
                    }

                    if ($MasterQty < 0 || $MasterQty === null) {
                        $stock = Config::getDefaultStock();
                    } else {
                        $stock = $MasterQty;
                    }

                    self::$stock = self::$stock + $stock;

					if (empty($val['sku'])) {
						$val['sku'] = $val['variation_id'];
                        /*
                        $val['sku'] = [ $val['variation_id'] ];
						if ($attribute['size'] !== null) { $val['sku'][] = $attribute['size']; }
						if ($attribute['color'] !== null) { $val['sku'][] = $attribute['color']; }
						$val['sku'] = implode('-', $val['sku']);
                        */
					}

                    $v = array(
                        'id' => $val['variation_id'],
                        'sku' => $val['sku'],
                        'acquisition_price' => self::getAcquisitionPrice(),
                        'price' => $val['display_regular_price'],
                        'sale_price' => $val['display_price'],
                        'availability' => self::checkAvailability($stock, $val['is_in_stock']),
                        'stock' => $stock,
                        'size' => $attribute['size'],
                        'color' => $attribute['color']
                    );

                    if (empty($v['size'])) {
                        unset($v['size']);
                    }

                    if (empty($v['color'])) {
                        unset($v['color']);
                    }

                    $lis[] = $v;
                }
            }
        }

        return $lis;
    }

    public static function toArray()
    {
        $data = array();

        foreach (self::$selfValue as $key=>$value) {
            $data[$key] = self::$value();
        }

        return $data;
    }
}