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

class Run
{
    private static $add = null;
    private static $ajax = null;
    private static $init = null;
    private static $pPath = null;
    private static $platform = null;
    public static $version = MKTR_VERSION;

    public static function init() {
        if (self::$init == null) { self::$init = new self(); }
        return self::$init;
    }

    public static function debug() {
        if (isset($_COOKIE['EAX_DEBUG'])) {
            var_dump(func_get_args());
            die();
        }
    }

    public static function plug_url($path = '')
    {
        if (self::$pPath === null) {
            self::$pPath = plugins_url(MKTR_DIR_NAME);
        }
        return self::$pPath . $path;
    }

    public static function platform() {
        if (self::$platform === null) {
            self::$platform = array(
                'name' => 'wordpress',
                'version' => \get_bloginfo( 'version' ),
                'mktr_version' => self::$version
            );

            if (function_exists('WC')) {
                self::$platform['woocommerce'] = \WC()->version;
            } else if (defined('WC_VERSION')) {
                self::$platform['woocommerce'] = \WC_VERSION;
            } else {
                $v = Config::getValue('woocommerce_version');
                if (empty($v)) {
                    self::$platform['woocommerce'] = 'unknown';
                } else {
                    self::$platform['woocommerce'] = $v;
                }
            }
        }
        return self::$platform;
    }

    public function __construct()
    {

        // register_deactivation_hook(__FILE__, [$this, 'unInstall']);
        Session::getUid();

	    add_action( 'activate_' . MKTR_BASE, [$this, 'Install']);
	    add_action( 'deactivate_' . MKTR_BASE, [$this, 'unInstall']);

        add_action( 'init', array($this, 'addRoute'), 0 );

        add_filter( 'gform_after_submission', array($this, 'gform_observer'), 10, 2 );
        
        // add_action( 'wp_ajax_ajaxregister', array( $this, 'registerOrLogIn' ));
        // add_action( 'wp_ajax_nopriv_ajaxregister', array( $this, 'registerOrLogIn' ));

        add_filter( 'woocommerce_registration_auth_new_customer', array($this, 'Register'), 10, 2);
        add_action( 'styler_after_login', array( $this, 'LogIn' ));

        add_action( 'before_woocommerce_init', function() {
            if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
                \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', MKTR, true );
            }
        });

        if (is_admin()) {
            Admin::loadAdmin();
        } else {
            Front::loadFront();
        }

        add_filter('woocommerce_add_to_cart_product_id', array($this, 'filter_add_to_cart'));

        add_action('wp_ajax_woocommerce_add_to_cart', array( $this, 'add_to_cart' ));
        add_action('wp_ajax_nopriv_woocommerce_add_to_cart', array( $this, 'add_to_cart' ));
        
        add_action('wp_ajax_woocommerce_ajax_add_to_cart', array( $this, 'add_to_cart_ajax' ));
        add_action('wp_ajax_nopriv_woocommerce_ajax_add_to_cart', array( $this, 'add_to_cart_ajax' ));

        // add_action('mailpoet_subscriber_updated', array( $this, 'mailpoet_subscription_status_changed' ));
        add_action('mailpoet_subscriber_status_changed', array( $this, 'mailpoet_subscription_status_changed' ));
        
        add_action('wp_ajax_mailpoet', array( $this, 'mailpoet_ajax' ));
        add_action('wp_ajax_nopriv_mailpoet', array( $this, 'mailpoet_ajax' ));

        add_action('woodmart_after_body_open', array( $this, 'woodmart_body' ), 600 );
        
        add_action('wp_ajax_basel_ajax_add_to_cart', array($this, 'add_to_cart'), 1);
        add_action('wp_ajax_nopriv_basel_ajax_add_to_cart', array($this, 'add_to_cart'), 1);

        add_action('wp_ajax_basel_add_to_wishlist', array($this, 'add_to_wishlist'), 1);
        add_action('wp_ajax_nopriv_basel_add_to_wishlist', array($this, 'add_to_wishlist'), 1);

        add_action('wp_ajax_basel_remove_from_wishlist', array($this, 'remove_from_wishlist'), 1);
        add_action('wp_ajax_nopriv_basel_remove_from_wishlist', array($this, 'remove_from_wishlist'), 1);

        add_action('wp_ajax_nopriv_woodmart_ajax_add_to_cart', array($this, 'add_to_cart'));
        add_action('wp_ajax_woodmart_ajax_add_to_cart', array($this, 'add_to_cart'));

        add_action('wp_ajax_nopriv_woodmart_add_to_wishlist', array($this, 'add_to_wishlist'));
        add_action('wp_ajax_woodmart_add_to_wishlist', array($this, 'add_to_wishlist'));

        add_action('wlfmc_added_to_wishlist', array($this, 'add_to_wishlist_wlfmc'));
        add_action('wlfmc_removed_from_wishlist', array($this, 'remove_from_wishlist_wlfmc'));
        add_action('wlfmc_before_delete_wishlist_item', array($this, 'delete_wishlist_item_wlfmc'));
		// add_action('wp_ajax_nopriv_wlfmc_add_to_wishlist', array($this, 'add_to_wishlist_wlfmc'));
        // add_action('wp_ajax_wlfmc_delete_item', array($this, 'remove_from_wishlist_wlfmc'));

        add_action('wp_ajax_nopriv_woodmart_remove_from_wishlist', array($this, 'remove_from_wishlist'));
        add_action('wp_ajax_woodmart_remove_from_wishlist', array($this, 'remove_from_wishlist'));

        add_action('wp_ajax_nopriv_add_to_wishlist', array($this, 'add_to_wishlist_yith'));
        add_action('wp_ajax_add_to_wishlist', array($this, 'add_to_wishlist_yith'));

        add_action('wp_ajax_nopriv_delete_item', array($this, 'remove_from_wishlist_item'));
        add_action('wp_ajax_delete_item', array($this, 'remove_from_wishlist_item'));

        add_action('wp_ajax_nopriv_remove_from_wishlist', array($this, 'remove_from_wishlist_yith'));
        add_action('wp_ajax_remove_from_wishlist', array($this, 'remove_from_wishlist_yith'));

        // add_action('wp_ajax_woodmart_ajax_add_to_cart', array(self::init(), 'test'));
        // add_action('woocommerce_loaded', function (){  });
        add_action('MKTR_CRON', array($this, "cronAction"));
    }

    public function mailpoet_subscription_status_changed($id = null){
        if ($id !== null) {
            Observer::mailpoet_status_changed($id);
        }
    }

    public function registerOrLogIn()
    {
        $email = Config::REQUEST('email');
        if ($email !== null) {
            Observer::emailAndPhone($email);
        }
    }
    
    public function LogIn($user)
    {
        if (isset($user->user_email)) {
            Observer::email($user);
        }
    }

    public function Register($status = null, $uID = null)
    {
        $user = get_user_by('id', $uID);
        if (isset($user->user_email)) {
            Observer::email($user);
        }
        return $status;
    }

    public function gform_observer($r, $f) {
        $newData = [
            'email_address' => null,
            'lastname' => '',
            'firstname' => ''
        ];
        foreach ($f['fields'] as $k => $v) {
            if (in_array($v->type, ['name'])) {
                foreach ($v->inputs as $kk => $vv) {
                    $rID = $vv['id'];
                    if (isset($r[$rID])) {
                        if (in_array(strtolower($vv['label']), ['last', 'suffix'])) {
                            $newData['lastname'] .= $r[$rID];
                        } else {
                            $newData['firstname'] .= $r[$rID];
                        }
                    }
                }
            } else if (in_array($v->type, ['email'])) {
                $rID = $v->inputs[0]['id'];
                if (isset($r[$rID])) {
                    $newData['email_address'] = $r[$rID];
                }
            }
        }
        
        if (!empty($newData['email_address'])) {
            $n = [];
            foreach ($newData as $k => $v) {
                if (!empty($v)) {
                    $n[$k] = $v;
                }
            }
            // $r['gform_submit']
            Observer::setGEmail($n, $r['form_id']);
        }
    }

    public function woodmart_body() {
        Events::$isWoodMart = true;
    }

    public function mailpoet_ajax() {
        if (self::$ajax === null && Config::REQUEST('method') === 'subscribe') {
            self::$ajax = true;
            $data = Config::REQUEST('data');
            if (is_array($data)) {
                foreach ($data as $k => $v) {
                    if (filter_var($v, FILTER_VALIDATE_EMAIL)) {
                        Observer::setEmail($v);
                        break;
                    }
                }
            }
        }
    }
    public function add_to_cart_ajax() {
        if (self::$add === null) {
            $product_id = Config::REQUEST('product_id');
            if ($product_id !== null) {
                self::$add = true;
                $quantity = Config::REQUEST('quantity');
                $variation_id = Config::REQUEST('variation_id');
                Observer::addToCart(
                    $product_id,
                    $quantity,
                    $variation_id !== null ? $variation_id : 0
                );
            }
        }
    }

    public function add_to_wishlist_yith()
    {
        $product_id = Config::REQUEST('add_to_wishlist');
        if ($product_id !== null) {
            Observer::addToWishlist($product_id, 0);
        }
    }

    public function remove_from_wishlist_yith()
    {
        $product_id = Config::REQUEST('remove_from_wishlist');
        if ($product_id !== null) {
            Observer::removeFromWishlist($product_id, 0);
        }
    }

    public function remove_from_wishlist_item()
    {
        $product_id = null; $fragments = isset( $_REQUEST['fragments'] ) ? wc_clean( $_REQUEST['fragments'] ) : [];
        foreach($fragments as $v) {
            if(isset($v['product_id'])) { $product_id = $v['product_id']; break; }
        }
       
        if ($product_id !== null) { Observer::removeFromWishlist($product_id, 0); }
    }

    public function remove_from_wishlist() {
        $product_id = Config::REQUEST('product_id');
        if ($product_id !== null) { Observer::removeFromWishlist($product_id, 0); }
    }

    public function add_to_wishlist() {
        $product_id = Config::REQUEST('product_id');
        if ($product_id !== null) {
            Observer::addToWishlist($product_id, 0);
        }
    }

	public function add_to_wishlist_wlfmc( $product_id = null ) {
        // $product_id = Config::REQUEST('add_to_wishlist');
        if ($product_id !== null) {
            Observer::addToWishlist($product_id, 0);
        }
    }
	public function remove_from_wishlist_wlfmc( $product_id = null ) {
        // $product_id = Config::REQUEST('wishlist_id');
        if ($product_id !== null) {
            Observer::removeFromWishlist($product_id, 0);
        }
    }
	public function delete_wishlist_item_wlfmc( $_id = null ) {
		$product_id = null;
		$wishlist_items = \WLFMC_Wishlist_Factory::get_current_wishlist();
		foreach($wishlist_items->get_items() as $w) {
            if($w->get_id() == $_id) { $product_id = $w->get_product_id(); break; }
        }
        if ($product_id !== null) {
            Observer::removeFromWishlist($product_id, 0);
        }
    }

    public function add_to_cart() {
        if (self::$add === null) {
            $addToCart = Config::REQUEST('add-to-cart');
            if ($addToCart !== null) {
                self::$add = true;
                $quantity = Config::REQUEST('quantity');
                if (is_array($quantity)) {
                    foreach ($quantity as $var=>$val) {
                        if (!empty($val)) {
                            Observer::addToCart(
                                $addToCart,
                                $val,
                                $var
                            );
                        }
                    }
                } else {
                    $variation_id = Config::REQUEST('variation_id');
                    Observer::addToCart(
                        $addToCart,
                        $quantity,
                        $variation_id !== null ? $variation_id : 0
                    );
                }
            }
        }
    }

    public function filter_add_to_cart($product_id) {
        if (self::$add === null) {
            $addToCart = Config::REQUEST('add-to-cart');
            if ($addToCart !== null) {
                self::$add = true;
                $quantity = Config::REQUEST('quantity');
                $variation_id = Config::REQUEST('variation_id');
                Observer::addToCart(
                    $addToCart,
                    $quantity,
                    $variation_id !== null ? $variation_id : 0
                );
            }
        }
        return $product_id;
    }

    public function cronAction() {
        \Mktr\Tracker\Model\Cron::cronAction();
    }

    public function addRoute() {
        if (MKTR_INSTALL) { self::Update(); }

        add_rewrite_tag('%'.Config::$name.'%', '([^&]+)');

        /* Todo: AddToActivate */
        add_rewrite_rule(
            Config::$name.'/([^/]+)/([^/]+)/?',
            'index.php?'.Config::$name.'=$matches[2]',
            'top'
        );
    }

    public static function Update()
    {
        \Mktr\Tracker\Routes\refreshJS::execute(false);
        \Mktr\Tracker\Observer::pushStatus();
        
        $name = MKTR_DIR . '/mktr.php';
        $content = file_get_contents($name);

        $newContent = str_replace(array(
            "define('MKTR', __FILE__)",
            "define('MKTR_DIR', dirname(__FILE__));",
            "define('MKTR_BASE', plugin_basename(MKTR));",
            "define('MKTR_DIR_NAME', basename(dirname(MKTR)));",
            "define('MKTR_INSTALL', true);"
        ), array(
            "define('MKTR', '".MKTR."')",
            "define('MKTR_DIR', '".MKTR_DIR."');",
            "define('MKTR_BASE', '".MKTR_BASE."');",
            "define('MKTR_DIR_NAME', '".MKTR_DIR_NAME."');",
            "define('MKTR_INSTALL', false);"
        ), $content);

        $file = fopen($name, 'w+');
        fwrite($file, $newContent);
        fclose($file);
    }

    public function Install() {
        Session::up();
        Config::setValue("redirect", 1);
        Config::setValue("onboarding", 0);
        Config::setValue("rated_install", time() + 1209600 );
		
        \wp_remote_post('https://connector.themarketer.com/feedback/install', array(
            'method'      => 'POST',
            'timeout'     => 5,
            'user-agent'  => 'mktr:'.\get_bloginfo( 'url' ),
            'body' => array(
                'status' => 1,
                't' => time(),
                'platform' => \Mktr\Tracker\Run::platform()
            )
        ));
    }

    public function unInstall() {
        Session::down();
        \wp_clear_scheduled_hook('MKTR_CRON');
        
        \wp_remote_post('https://connector.themarketer.com/feedback/install', array(
            'method'      => 'POST',
            'timeout'     => 5,
            'user-agent'  => 'mktr:'.\get_bloginfo( 'url' ),
            'body' => array(
                'status' => 0,
                't' => time(),
                'platform' => \Mktr\Tracker\Run::platform()
            )
        ));
    }
}
