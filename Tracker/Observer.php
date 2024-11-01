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

use Mktr\Tracker\Model\Product;

class Observer
{
    private static $init = null;
    private static $eventName = null;
    private static $lastKey = null;
    private static $eventData = [];

    private static $OrderUP = false;
    private static $mStatusChange = false;

    private static $addToCart = false;
    private static $removeFromCart = false;

    private static $setEmailStatus = false;

    public static function init()
    {
        if (self::$init == null) {
            self::$init = new self();
        }
        return self::$init;
    }

    public static function addToCart($product_id, $quantity, $variation_id)
    {
        if (self::$addToCart === false) {
            self::$addToCart = true;
            $v = Product::getById($variation_id ?: $product_id);
            if ($v !== false) {
                self::$eventName = 'addToCart';
                $quantity = (int) $quantity;
                if ($quantity <= 0) {
                    $quantity = 1;
                }
                self::$eventData = array(
                    'product_id' => Product::getParentId() == 0 ? Product::getId() : Product::getParentId(),
                    'quantity' => $quantity,
                    'variation' => array(
                        'id' => Product::getId(),
                        'sku' => Product::getSku()
                    )
                );

                self::SessionSet(self::$eventData['product_id'].'.'.self::$eventData['quantity'].'.'.(int) $variation_id);
            }
        }
    }

    public static function removeFromCart($product_id, $quantity, $variation_id = null)
    {

        if (self::$removeFromCart === false) {
            self::$removeFromCart = true;
            Product::getById($variation_id ?: $product_id);

            self::$eventName = 'removeFromCart';
            $quantity = (int) $quantity;
            if ($quantity <= 0) {
                $quantity = 1;
            }
            self::$eventData = array(
                'product_id' => Product::getParentId() == 0 ? Product::getId() : Product::getParentId(),
                'quantity'=> $quantity,
                'variation' => array(
                    'id' => Product::getId(),
                    'sku' => Product::getSku()
                )
            );

            self::SessionSet(self::$eventData['product_id'].'.'.self::$eventData['quantity'].'.'.(int) $variation_id);
        }
    }
    public static function addToWishlist($product_id, $variation_id = null)
    {
        Product::getById($variation_id ?: $product_id);

        self::$eventName = 'addToWishlist';
        /*
        $quantity = (int) $quantity;
        if ($quantity <= 0) {
            $quantity = 1;
        }
        */

        self::$eventData = array(
            'product_id' => Product::getParentId() == 0 ? Product::getId() : Product::getParentId(),
            //'quantity'=> $quantity,
            'variation' => array(
                'id' => Product::getId(),
                'sku' => Product::getSku()
            )
        );

        self::SessionSet(self::$eventName.self::$eventData['product_id'].self::$eventData['variation']['sku']);
    }

    public static function removeFromWishlist($product_id, $variation_id = null)
    {
        Product::getById($variation_id ?: $product_id);

        self::$eventName = 'removeFromWishlist';
        /*
        $quantity = (int) $quantity;
        if ($quantity <= 0) {
            $quantity = 1;
        }
        */

        self::$eventData = array(
            'product_id' => Product::getParentId() == 0 ? Product::getId() : Product::getParentId(),
            //'quantity'=> $quantity,
            'variation' => array(
                'id' => Product::getId(),
                'sku' => Product::getSku()
            )
        );

        self::SessionSet(self::$eventName.self::$eventData['product_id'].self::$eventData['variation']['sku']);
    }

    public static function pushStatus()
    {
        FileSystem::setWorkDirectory('base');

        if (Config::getPushStatus() != 0) {
            FileSystem::writeFile("firebase-config.js", Config::getFireBase());
            FileSystem::writeFile("firebase-messaging-sw.js", Config::getFireBaseMessaging());
        } else {
            FileSystem::deleteFile("firebase-config.js");
            FileSystem::deleteFile("firebase-messaging-sw.js");
        }
    }

    public static function orderUp($oID = null, $status = null)
    {
        if ($oID !== null && $status !== null && self::$OrderUP === false) {
            self::$OrderUP = true;
            $send = array(
                'order_number' => $oID,
                'order_status' => $status
            );
    
            Api::send("update_order_status", $send, false);
            Logs::debug($send, 'update_order_status');
        }
    }
    public static function mailpoet_status_changed( $i = null ) {
        if ($i !== null && self::$mStatusChange === false) {
            self::$mStatusChange = true;
            
            if (class_exists(\MailPoet\Subscribers\SubscribersRepository::class)) {
                $repo = \MailPoet\DI\ContainerWrapper::getInstance()->get(\MailPoet\Subscribers\SubscribersRepository::class);
                $s = $repo->findOneBy(['id' => (int) $i]);
            } else {
                $s = \MailPoet\Models\Subscriber::findOne((int) $i);
            }
            
            $check = Config::session()->get('emailSend');
            $time = time();
            
            if ($check !== null && isset($check[$s->email])) {
                if (($time - $check[$s->email]) <= 60) {
                    Logs::debug($s->email, 'emailSendBlock'); 
                    return true;
                }
            } else if ($check === null){
                $check = [];
            }

            $info = array( "email" => $s->email );
            $gSub = Config::getSubscriber($s->email);

            if ($gSub !== false) {
                if (is_array($gSub)) {
                    $gSub = (object) $gSub;
                }
                $status = $gSub->status;
            } else {
                $status = "NotFound";
            }

            if ($status === Config::mStatus())
            {
                $name = array();

                if (!empty($s->first_name)) {
                    $name[] = $s->first_name;
                }

                if (!empty($s->last_name)) {
                    $name[] = $s->last_name;
                }

                if (empty($name)) {
                    $info["name"] = explode("@", $info['email'])[0];
                } else {
                    $info["name"] = implode(" ", $name);
                }

                $user = get_user_by('email', $info['email']);
                $phone = get_user_meta($user->ID, 'billing_phone', true);

                if (!empty($phone)) {
                    $info["phone"] = $phone;
                }

                Api::send("add_subscriber", $info);
                Logs::debug($info, 'add_subscriber');
            } else {
                Api::send("remove_subscriber", $info);
                Logs::debug($info, 'remove_subscriber');
            }
            $check[$s->email] = $time;
            Config::session()->set('emailSend', $check);
        }
    }

    public static function orderUpApi($oID = null, $order = null)
    {
        if (self::$OrderUP === false && $oID !== null && $order !== null && $order->get_status() !== 'checkout-draft') {
            // FileSystem::setWorkDirectory('base');
            // FileSystem::writeFile("baseTest.js",'baseLinkUpdate');
            self::$OrderUP = true;

            $send = array( 'order_number' => $oID, 'order_status' => $order->get_status() );

            Api::send("update_order_status", $send, false);
            Logs::debug($send, 'update_order_status');
        }
    }

    public static function saveOrder($orderId = null) {
        // Order::getById($orderId);
        // ['email_address']
        // ['phone']

        self::$eventName = 'saveOrder';
        self::$eventData = $orderId;

        self::SessionSet($orderId);
    }

    public static function registerOrLogIn($user_login, $user = null) {
        if (!is_null($user)) {
            setcookie("mktr", sanitize_email(( is_array($user) ? $user["user_email"] : $user->user_email )), strtotime('+30 days'), COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true);
        }
    }

    public static function getEmail($email = null, $user = null) {
        if ($user === null) {
            $user = get_user_by('email', $email);
        }

        if ( $user->user_email !== null ) {
            $email = $user->user_email;
        }

        $send = array(
            'email_address' => $email
        );

        if (!empty($user->first_name)) {
            $send['firstname'] = $user->first_name;
        } else {
            $send['firstname'] = get_user_meta($user->ID, 'billing_first_name', true);
            if (empty($send['firstname'])) {
                unset($send['firstname']);
            }
        }

        if (!empty($user->last_name)) {
            $send['lastname'] = $user->last_name;
        } else {
            $send['lastname'] = get_user_meta($user->ID, 'billing_last_name', true);
            if (empty($send['lastname'])) {
                unset($send['lastname']);
            }
        }
        return $send;
    }

    public static function emailAndPhone($email)
    {
        if (self::$setEmailStatus === false) {
            self::$setEmailStatus = true;
            $user = get_user_by('email', $email);

            $send = self::getEmail($email, $user);
    
            self::$eventName = "setPhone";
    
            self::$eventData = array( 'phone' => get_user_meta($user->ID, 'billing_phone', true) );
            
            if (!empty(self::$eventData['phone']) && self::$eventData['phone'] !== '') {
                self::SessionSet(self::$eventData['phone']);
            }
    
            self::$eventName = 'setEmail';
            self::$eventData = $send;
    
            self::SessionSet(self::$eventData['email_address']);
        }
    }

    public static function email($user)
    {
        if (self::$setEmailStatus === false) {
            self::$setEmailStatus = true;
            $send = self::getEmail($user->user_email, $user);

            self::$eventName = "setPhone";

            self::$eventData = array(
                'phone' => get_user_meta($user->ID, 'billing_phone', true)
            );

            self::SessionSet(self::$eventData['phone']);

            self::$eventName = 'setEmail';
            self::$eventData = $send;

            self::SessionSet(self::$eventData['email_address']);
        }
    }
    
    public static function setEmail($email)
    {
        if (self::$setEmailStatus === false) {
            self::$setEmailStatus = true;
            self::$eventName = 'setEmail';
            self::$eventData = array( 'email_address' => $email );
            self::SessionSet(self::$eventData['email_address']);
        }
    }

    public static function setGEmail($data, $gID = null)
    {
        if (self::$setEmailStatus === false) {
            self::$setEmailStatus = true;
            self::$eventName = 'setEmail';
            
            self::$eventData = $data;
            self::SessionSet(self::$eventData['email_address']);
            if ($gID !== null) {
                $gEV = 'gform';
                $add = Config::session()->get($gEV);
                $add[self::$lastKey] = $gID;
                Config::session()->set($gEV, $add);
            }
        }
    }

    private static function SessionSet($key = null)
    {
        $add = Config::session()->get(self::$eventName);

        if ($key === null) {
            $n = '';
            for ($i = 0, $indexMax = 9; $i < 5; ++$i) { $n .= random_int(0, 9); }
            self::$lastKey = time().$n;
        } else {
            self::$lastKey = $key;
        }

        $add[self::$lastKey] = self::$eventData;

        Config::session()->set(self::$eventName, $add);
    }
}
