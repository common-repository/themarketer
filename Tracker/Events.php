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

use Mktr\Tracker\Model\Order;
use Mktr\Tracker\Model\Category;
use Mktr\Tracker\Model\Product;

class Events
{
    private static $init = null;
    private static $shName = null;
    private static $data = array();

    public static $isWoodMart = false;
    private static $load_js = true;
    
    private static $assets = array();

    const actions = [
        "is_home" => "__sm__view_homepage",
        "is_product_category" => "__sm__view_category",
        "is_product" => "__sm__view_product",
        "is_checkout" => "__sm__initiate_checkout",
        "is_search" => "__sm__search"
    ];

    const observerGetEvents = [
        "addToCart"=> [false, "__sm__add_to_cart"],
        "removeFromCart"=> [false, "__sm__remove_from_cart"],
        "addToWishlist"=> [false, "__sm__add_to_wishlist"],
        "removeFromWishlist"=> [false, "__sm__remove_from_wishlist"],
        "saveOrder"=> [true, "__sm__order"],
        "setEmail"=> [true, "__sm__set_email"],
        "setPhone"=> [false, "__sm__set_phone"]
    ];

    const eventsName = [
        "__sm__view_homepage" =>"HomePage",
        "__sm__view_category" => "Category",
        "__sm__view_brand" => "Brand",
        "__sm__view_product" => "Product",
        "__sm__add_to_cart" => "addToCart",
        "__sm__remove_from_cart" => "removeFromCart",
        "__sm__add_to_wishlist" => "addToWishlist",
        "__sm__remove_from_wishlist" => "removeFromWishlist",
        "__sm__initiate_checkout" => "Checkout",
        "__sm__order" => "saveOrder",
        "__sm__search" => "Search",
        "__sm__set_email" => "setEmail",
        "__sm__set_phone" => "setPhone"
    ];

    const eventsSchema = [
        "HomePage" => null,
        "Checkout" => null,
        "Cart" => null,

        "Category" => [
            "category" => "category"
        ],

        "Brand" => [
            "name" => "name"
        ],

        "Product" => [
            "product_id" => "product_id"
        ],

        "Search" => [
            "search_term" => "search_term"
        ],

        "setPhone" => [
            "phone" => "phone"
        ],

        "addToWishlist" => [
            "product_id" => "product_id",
            "variation" => [
                "@key" => "variation",
                "@schema" => [
                    "id" => "id",
                    "sku" => "sku"
                ]
            ]
        ],

        "removeFromWishlist" => [
            "product_id" => "product_id",
            "variation" => [
                "@key" => "variation",
                "@schema" => [
                    "id" => "id",
                    "sku" => "sku"
                ]
            ]
        ],

        "addToCart" => [
            "product_id" => "product_id",
            "quantity" => "quantity",
            "variation" => [
                "@key" => "variation",
                "@schema" => [
                    "id" => "id",
                    "sku" => "sku"
                ]
            ]
        ],

        "removeFromCart" => [
            "product_id" => "product_id",
            "quantity" => "quantity",
            "variation" => [
                "@key" => "variation",
                "@schema" => [
                    "id" => "id",
                    "sku" => "sku"
                ]
            ]
        ],

        "saveOrder" => [
            "number" => "number",
            "email_address" => "email_address",
            "phone" => "phone",
            "firstname" => "firstname",
            "lastname" => "lastname",
            "city" => "city",
            "county" => "county",
            "address" => "address",
            "discount_value" => "discount_value",
            "discount_code" => "discount_code",
            "shipping" => "shipping",
            "tax" => "tax",
            "total_value" => "total_value",
            "products" => [
                "@key" => "products",
                "@schema" =>
                    [
                        "product_id" => "product_id",
                        "price" => "price",
                        "quantity" => "quantity",
                        "variation_sku" => "variation_sku"
                    ]
            ]
        ],

        "setEmail" => [
            "email_address" => "email_address",
            "firstname" => "firstname",
            "lastname" => "lastname"
        ]
    ];
    /**
     * @var array
     */
    private static $bMultiCat;
    private static $listName = [];

    public static function init()
    {
        if (self::$init == null) {
            self::$init = new self();
        }
        return self::$init;
    }

    public static function mktr_data()
    {
        $mktr_data = array(
            'uuid'=> null,
            'clear' => 0,
            'isWoodMart' => (int) self::$isWoodMart,
            'push' => array(),
            'BaseURL' => Config::getBaseURL(),
            'js' => array()/* ,
            'evData' => \Mktr\Tracker\Routes\loadEvents::execute(false) */
        );

        if ($mktr_data['isWoodMart']) {
            $wishList = Config::session()->get("woodmart_wishlist_products");
            if ($wishList === null) {
                $wishList = (isset($_COOKIE['woodmart_wishlist_products']) ? $_COOKIE['woodmart_wishlist_products'] : '{}');
                Config::session()->set("woodmart_wishlist_products", $wishList);
                Config::session()->set("woodmart_wishlist_count", (isset($_COOKIE['woodmart_wishlist_count']) ? $_COOKIE['woodmart_wishlist_count'] : 0));
            }
            $mktr_data['wishList'] = $wishList;
        }
        if (Session::$saveCookie) {
            $mktr_data['uuid'] = Session::getUid();
        }
        
        $clear = Config::session()->get("ClearMktr");

        if ($clear === null) { $clear = array(); }

        $saveOrder = false;

        foreach (self::observerGetEvents as $event=>$Name) {
            $eventData = Config::session()->get($event);
            if (!empty($eventData)) {
                if ( in_array($event, ["saveOrder", "setEmail", "setPhone"]) ) {
                    foreach ($eventData as $key=>$value) {
                        $ev = self::getEvent($Name[1], $value);
                        if ($event === "saveOrder") { $saveOrder = true; }
                        if ($ev !== false) {
                            $mktr_data['push'][] = $ev->toArray();
                            if (!$Name[0]) { $clear[$event][$key] = $key; }
                        }
                    }
                }

                if ($Name[0]) {
                    //Config::session()->set($event, array());
                    $mktr_data['js'][$event] = true;
                } /** @noinspection PhpStatementHasEmptyBodyInspection */ else {
                    // $clear[$event][$key] = "clear";
                    // Config::session()->set($event, array());
                }
            }
        }

        foreach (self::actions as $key => $value) {
            if ( $key === 'is_checkout'&& $saveOrder === false && $key() || $key !== 'is_checkout' && $key() || $key === 'is_home' && is_front_page() ) {
                $ev = self::getEvent($value);
                if ($ev !== false) {
                    $mktr_data['push'][] = $ev->toArray();
                }
                break;
            }
        }
        if (!empty($clear)) {
            Config::session()->set("ClearMktr", $clear);
            $mktr_data['clear'] = 1;
        }
        return $mktr_data;
    }
    
    public function initEvents() {
        if (self::$load_js) {
            self::$load_js = false;
            $js_file = Config::getValue('js_file');

            if ( $js_file !== null ) {
                wp_enqueue_script('mktr-loader', Run::plug_url('/assets/mktr.'.$js_file.'.js'), array(), false, array('strategy'  => 'async'));
                $mktr_data = self::mktr_data();
                wp_localize_script('mktr-loader', 'mktr_data', $mktr_data);
            }
        }
    }

    public static function loader()
    {
        if (self::$load_js) {
            self::$load_js = false;
            $js_file = Config::getValue('js_file');
            
            if ( $js_file !== null ) {
                $mktr_data = self::mktr_data();

                $content = "<!-- Mktr Script Start -->";
                $content .= '<script type="text/javascript" async>';
                if (!empty($mktr_data)) {
                    $content .= 'window.mktr_data = '. json_encode($mktr_data, true) .';';
                }
                $content .= '</script><script async src="'.Run::plug_url('/assets/mktr.'.$js_file.'.js').'" ></script>';
                $content .= "<!-- Mktr Script END -->";
                echo $content;
            }
        }
    }

    public static function build()
    {
        foreach (self::$assets as $key=>$val) {
            self::$data[$key] = $val;
        }
    }

    public static function schemaValidate($array, $schema)
    {
        $newOut = [];

        foreach ($array as $key=>$val) {
            if (isset($schema[$key])) {
                if (is_array($val)) {
                    $newOut[$schema[$key]["@key"]] = self::schemaValidate($val, $schema[$key]["@schema"]);
                } else {
                    $newOut[$schema[$key]] = $val;
                }
            } elseif (is_array($val)) {
                $newOut[] = self::schemaValidate($val, $schema);
            }
        }

        return $newOut;
    }

    public static function getEvent($Name, $eventData = [])
    {
        if (empty(self::eventsName[$Name])) {
            return false;
        }

        self::$shName = self::eventsName[$Name];

        self::$data = array(
            "event" => $Name
        );

        self::$assets = array();

        switch (self::$shName) {
            case "Category":
                self::$assets['category'] = self::buildCategory();
                break;
            case "Product":
                if (Product::getId() !== null) {
                    self::$assets['product_id'] = Product::getId();
                } else {
                    return false;
                }
                break;
            case "saveOrder":
                Order::getById($eventData);
                self::$assets = Order::toArray();
                if (empty(self::$assets['products']) || (empty(self::$assets['email_address']) && empty(self::$assets['phone']))) {
                    return false;
                }
                break;
            case "Search":
                self::$assets['search_term'] = get_search_query(true);
                break;
            default:
                self::$assets = $eventData;
        }

        self::$assets = self::schemaValidate(self::$assets, self::eventsSchema[self::$shName]);

        self::build();

        return self::init();
    }

    public static function buildCategory($categoryRegistry = null)
    {
        if ($categoryRegistry == null) {
            $categoryRegistry = Category::init();
        }

        $build = array($categoryRegistry->getName());

        while ($categoryRegistry->getParentId() > 0) {
            $categoryRegistry = Category::getById($categoryRegistry->getParentId());
            $build[] = $categoryRegistry->getName();
        }
        return implode("|", array_reverse($build));
    }

    public static function buildMultiCategory($List) {
        $buildMultiCat = [];
        $newBuild = [];
        $NewMultiCat = [];
		
        $skip = [];
        if(is_array($List)){
            foreach ($List as $value) {
            self::$listName[$value->term_id] = $value->name;
                if ($value->parent == 0) {
                    if (!isset($buildMultiCat[$value->term_id])) {
                        $buildMultiCat[$value->term_id] = [];
                    }
                } else {
                    $buildMultiCat[$value->parent]["id".$value->term_id] = $value->term_id;
                }
            }
        }

        foreach ($buildMultiCat as $parent => $categoryTree) {
            if (!in_array($parent, $skip)) {
                if (empty($categoryTree)) {
                    $list = [ $parent => self::getNameCat($parent) ];
                    $newBuild[] = $list;
                } else {
                    foreach($categoryTree as $key => $input) {
                        $list = [ $parent => self::getNameCat($parent) ];
                        if(isset($buildMultiCat[$input])) {
                            if ($parent !== $input) {
                                $skip[$input] = $input;
                                $list[$input] = self::getNameCat($input);

                                self::bMC($buildMultiCat, $buildMultiCat[$input], $input, $list);
                            }
                        } else {
                            $list[$input] = self::getNameCat($input);
                        }
                        $newBuild[] = $list;
                    }
                }
            }
        }

        if (empty($newBuild)) {
            $newBuild[0]['default'] = "Default Category";
        }
        
        foreach ($newBuild as $k => $data) {
            $imp = implode('|', $data);
            $NewMultiCat[$imp] = $imp;
        }

        $categoriesTree = implode('||', $NewMultiCat);
        
        return $categoriesTree;
    }

    public static function getNameCat($id) {
        if (!isset(self::$listName[$id])) {
            $cat = get_term_by( 'id', $id, 'product_cat' );
            self::$listName[$id] = $cat->name;
        }
        return self::$listName[$id];
    }

    public static function bMC($buildMultiCat, $catList, $parent, &$list) {
        foreach ($catList as $cat) {
            if ($cat !== $parent) {
                if(isset($buildMultiCat[$cat])) {
                    foreach(self::bMC($buildMultiCat, $buildMultiCat[$cat], $cat, $list) as $k) {
                        $list[$k] = self::getNameCat($k);
                    }
                } else {
                    $list[$cat] = self::getNameCat($cat);
                }
            }
        }
        return $list;
    }
	
    public static function buildSingleCategory()
    {
        self::$bMultiCat[] = Category::getName();

        while (Category::getParentId() > 0) {
            Category::getById(Category::getParentId());

            self::$bMultiCat[] = Category::getName();
        }
    }

    public function toArray()
    {
        return self::$data;
    }

    public function toJson()
    {
        return Valid::toJson(self::$data);
    }
}
