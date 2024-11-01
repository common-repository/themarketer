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

use Mktr\Tracker\Events;
use Mktr\Tracker\Valid;
use Mktr\Tracker\Config;

class loadEvents
{
    private static $init = null;

    private static $map = array();

    public static function get($f = 'fileName')
    {
        if (isset(self::$map[$f])) {
            return self::$map[$f];
        }
        return null;
    }

    public static function init() {
        if (self::$init == null) { self::$init = new self(); }
        return self::$init;
    }

    public static function checkAdded($n, $o) {
        if ($n === null && $o === null) { return []; }
        if ($n === null) { return $o; }
        if ($o === null) { return $n; }
        return array_keys(array_diff_key($n, $o));
    }
    
    public static function checkDif($cart, $cartNew, &$remove = [], &$add = []) {
        foreach ($cart as $k => $v) {
            if (isset($cartNew[$k])) {
                if($v["quantity"] != $cartNew[$k]["quantity"]){
                    if ($v["quantity"] < $cartNew[$k]["quantity"]) {
                        $add[$k] = $cartNew[$k];
                        $add[$k]["quantity"] = $cartNew[$k]["quantity"] - $v["quantity"];
                    } else if ($v["quantity"] > $cartNew[$k]["quantity"]) {
                        $remove[$k] = $cartNew[$k];
                        $remove[$k]["quantity"] = $v["quantity"] - $cartNew[$k]["quantity"];
                    }
                }
            } else {
                $remove[$k] = $v;
            }
        }
    }

    public static function execute( $mime = true ) {
        // Valid::setParam('mime-type', 'js');
        // $lines = [ '/* TheMaketer */' ];
        $lines = array();
        if ($mime) {
            Valid::setParam('mime-type', 'json');
        }
        $wishListC = Config::session()->get("woodmart_wishlist_count");
        if ($wishListC !== null || isset( $_COOKIE['woodmart_wishlist_count'] )) {
            $wishList = Config::session()->get("woodmart_wishlist_products");
            if ($wishListC === null) {
                $wishListC = 0;
            }
            if ($wishList === null) {
                $wishList = '{}';
            }
            if ($wishList === null || $wishListC != $_COOKIE['woodmart_wishlist_count']) {
                $wishList0 = (isset($_COOKIE['woodmart_wishlist_products']) ? $_COOKIE['woodmart_wishlist_products'] : '{}');
                if ($wishListC !== null && $wishListC != $_COOKIE['woodmart_wishlist_count']) {
                    $n = json_decode(stripslashes($wishList0), true); $o = json_decode(stripslashes($wishList), true);
                    
                    $add = self::checkAdded($n, $o);
                    $remove = self::checkAdded($o, $n);
                    foreach ($add as $value) { \Mktr\Tracker\Observer::addToWishlist($value, 0); }
                    foreach ($remove as $value) { \Mktr\Tracker\Observer::removeFromWishlist($value, 0); }
                }
                Config::session()->set("woodmart_wishlist_products", $wishList0);
                Config::session()->set("woodmart_wishlist_count", $_COOKIE['woodmart_wishlist_count']);
            }
        }

        $cartNew = [];
        $cart = Config::session()->get("mktr_cart");
        if ($cart === null) { $cart = []; }
        if ( isset( WC()->cart ) && is_array( WC()->cart->cart_contents ) && ! empty( WC()->cart->cart_contents ) ) {
			foreach ( WC()->cart->cart_contents as $cart_item ) {
				$cartNew[$cart_item["product_id"].'.'.$cart_item["variation_id"]] = [
                    "product_id" => $cart_item["product_id"],
                    "quantity" => $cart_item["quantity"],
                    "variation_id" => $cart_item["variation_id"]
                ];
			}
		}

        $add = [];
        $remove = [];

        self::checkDif($cart, $cartNew, $remove, $add);
        self::checkDif($cartNew, $cart, $add, $remove);

        if (!empty($add) || !empty($remove)) {
            Config::session()->set("mktr_cart", $cartNew);
        }
        
        foreach ($add as $i => $v) {
            \Mktr\Tracker\Observer::addToCart($v["product_id"], $v["quantity"], $v["variation_id"]);
        }

        foreach ($remove as $i => $v) {
            \Mktr\Tracker\Observer::removeFromCart($v["product_id"], $v["quantity"], $v["variation_id"]);
        }

        foreach (Events::observerGetEvents as $event => $Name) {
            if (!$Name[0]) {
                $eventData = Config::session()->get($event);
                if (!empty($eventData)) {
                    foreach ($eventData as $value) {
                        //$lines[] = "dataLayer.push(" . Events::getEvent($Name[1], $value)->toJson() . ");";
                        $ev = Events::getEvent($Name[1], $value);
                        if ($ev !== false) {
                            $lines[] = $ev->toArray();
                        }
                    }
                }
                Config::session()->set($event, array());
            }
        }
        
        return $lines;
        // return implode(PHP_EOL, $lines);
    }
}