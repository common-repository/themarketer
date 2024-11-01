<?php
/**
 * @copyright   Copyright (c) 2023 TheMarketer.com
 * @project     TheMarketer.com
 * @website     https://themarketer.com/
 * @author      Alexandru Buzica (EAX LEX S.R.L.) <b.alex@eax.ro>
 * @license     https://opensource.org/licenses/osl-3.0.php - Open Software License (OSL 3.0)
 * @docs        https://themarketer.com/resources/api
 */

namespace Mktr\Tracker;

class Front
{
    private static $init = null;

    public static $Page = false;

    public static $RemoveCartEvent = true;
    public static $saveOrderEvent = true;
    public static $addCartEvent = true;

    public static function init()
    {
        if (self::$init == null) {
            self::$init = new self();
        }
        return self::$init;
    }

    public static function loadFront()
    {
        if (Config::getOnboarding() === 2 && Config::getStatus() === 1 && !empty(Config::getKey())) {
            add_action('template_redirect', array(self::init(), 'routeCheck'));
            add_action('wp_login', array(self::init(), 'registerOrLogIn'), 10, 2);
            add_action('user_register', array(self::init(), 'registerOrLogIn'), 10, 2);

            // add_action('woocommerce_loaded', array(self::init(), 'LoadSession'));
            
            add_action('woocommerce_loaded', array(self::init(), 'loadModule'));
            add_action('woocommerce_update_order', array(Observer::init(), 'orderUpApi'), 10, 2);
        } else {
            add_action('template_redirect', array(self::init(), 'routeCheck'));
        }

        //add_action('shutdown', array($this, 'sd'), 0);
    }

    public function registerOrLogIn($user_login, $user = null)
    {
        Observer::registerOrLogIn($user_login, $user);
    }

    public function saveOrder($orderId = null)
    {
        if ($orderId !== null && self::$saveOrderEvent) {
            Config::session()->set("mktr_cart", []);
            self::$saveOrderEvent = false;
            Observer::saveOrder($orderId);
        }
    }

    public function saveOrder1($orderId = null, $checkout = null )
    {
        // Logs::debug($orderId, 'saveOrder1');
        if ($orderId !== null && self::$saveOrderEvent) {
            Config::session()->set("mktr_cart", []);
            self::$saveOrderEvent = false;
            Observer::saveOrder($orderId);
        }
    }

    public function loadModule() {

        add_action('woocommerce_before_thankyou', array(self::init(), 'saveOrder'));
        add_action('woocommerce_thankyou', array(self::init(), 'saveOrder'));
        add_action('woocommerce_new_order', array(self::init(), 'saveOrder'), 10, 2);

        add_action('wp_head', array(Events::init(), 'loader'));
        add_action('wp_enqueue_scripts', array(Events::init(), 'initEvents') );

        // add_filter('woocommerce_create_order', array(self::init(), 'saveOrder1'), 10, 2 );

        // AddToCart events
        // add_action('woocommerce_add_to_cart', array(self::init(), 'AddCartEvent'), 40, 4);
        // add_action('woocommerce_remove_cart_item', array(self::init(), 'RemoveCartEvent'), 10, 2);
        // add_filter('woocommerce_cart_item_removed_title', array(self::init(), 'RemoveCartEventFilter'), 10, 2);

        // AddToCart while AJAX is enabled
        // add_action('woocommerce_ajax_added_to_cart',  array($this, 'AddCartEvent'));
        // add_action('wp_footer', array(Events::init(), 'loadEvents'));
        
        /*
        add_filter('woocommerce_email_enabled_customer_new_account', function ($status) {
            if (Config::getOptIn() == 0) {
                return $status;
            }
            return false;
        });
        */
    }

    /** @noinspection PhpUnusedParameterInspection */
    public static function AddCartEvent($frg = null, $product_id = null, $quantity = null, $variation_id = null)
    {
        if (self::$addCartEvent) {
            self::$addCartEvent = false;
            Observer::addToCart(
                $product_id === null ? Config::POST('product_id') : $product_id,
                $quantity === null ? Config::POST('quantity') : $quantity,
                $variation_id === null ? 0 : $variation_id
            );
        }
    }

    public static function RemoveCartEvent($item, $cart = null)
    {
        if (self::$RemoveCartEvent) {
            self::$RemoveCartEvent = false;
            $cart = $cart->cart_contents[$item];
            Observer::removeFromCart($cart['product_id'], $cart['quantity'], $cart['variation_id']);
        }
    }

    public static function RemoveCartEventFilter($item, $cart = null)
    {
        if (self::$RemoveCartEvent) {
            self::$RemoveCartEvent = false;
            Observer::removeFromCart($cart['product_id'], $cart['quantity'], $cart['variation_id']);
            return $item;
        }
    }

    public function routeCheck()
    {
        if (isset($_COOKIE['mktr'])) {
            Observer::emailAndPhone($_COOKIE['mktr']);
            setcookie("mktr", '', 0, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true);
            unset($_COOKIE['mktr']);
        }

        self::$Page = get_query_var(Config::$name, false);

        if (self::$Page === false) {
            $p = array();
            $path = parse_url(sanitize_text_field($_SERVER['REQUEST_URI']), PHP_URL_PATH);
            preg_match("/([^\/]+)\/([^\/]+)\/([^\/]+)/i", $path, $p);

            $ch = array( Config::$name => false, 'api' => false );

            unset($p[0]);
            foreach ($p as $v) {
                if (!empty($v)) {
                    if ($ch[Config::$name] && $ch['api']) {
                        self::$Page = $v;
                    } elseif ($v === Config::$name) {
                        $ch[Config::$name] = true;
                    } elseif ($v === 'api') {
                        $ch['api'] = true;
                    }
                }
            }
        }

        if (self::$Page !== false) {
            Route::checkPage(self::$Page);
        }
    }
}
