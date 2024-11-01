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

use Mktr\Tracker\Api;
use Mktr\Tracker\Observer;
use Mktr\Tracker\Valid;
use Mktr\Tracker\Config;
use Mktr\Tracker\Model\Order;

class saveOrder
{
    private static $init = null;

    private static $map = array();
    
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
        if ( ! function_exists( 'is_plugin_active' ) ) {
            require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
        }
        
        Valid::setParam('mime-type', 'json');

        $Order = Config::session()->get('saveOrder');
        $allGood = true;
        //$installed_plugins = get_plugins();
        $plug = 'mailpoet/mailpoet.php';

        // $installed = array_key_exists($plug , $installed_plugins ) || in_array($plug, $installed_plugins, true );
        $active    = \is_plugin_active($plug);

        if (!empty($Order)) {
            $check = Config::session()->get('emailSend');
            $time = time();

            if ($check === null) { $check = []; }

            foreach ($Order as $sOrder1)
            {
                Order::getById($sOrder1);

                $sOrder = Order::toArray();

                if (!empty($sOrder['products']) && (!empty($sOrder['email_address']) || !empty($sOrder['phone'])) ) {
					Api::send("save_order", $sOrder);
                    \Mktr\Tracker\Logs::debug($sOrder, 'save_order');
                
					if (Api::getStatus() != 200) {
						$allGood = false;
					}
				} else {
					$allGood = false;
				}

                if ( $allGood && $active && !empty($sOrder['email_address']) )
                {
                    $val = Observer::getEmail($sOrder['email_address']);

                    $info = array( "email" => $sOrder['email_address'] );
                    if ($sOrder['email_address'] !== null) {
                        $gSub = Config::getSubscriber($sOrder['email_address']);

                        if ($gSub !== false) {
                            if (is_array($gSub)) {
                                $gSub = (object) $gSub;
                            }
                            $status = $gSub->status;
                        } else {
                            $status = "NotFound";
                        }
                        
                        if ($status === Config::mStatus()) {
                            if ($check !== null && isset($check[$sOrder['email_address']])) {
                                if (($time - $check[$sOrder['email_address']]) <= 60) {
                                    \Mktr\Tracker\Logs::debug($sOrder['email_address'], 'emailSendBlockSetEmail'); 
                                    continue;
                                }
                            }
                            $name = array();

                            if (!empty($val['firstname'])) { $name[] = $val['firstname']; }

                            if (!empty($val['lastname'])) { $name[] = $val['lastname']; }

                            if (empty($name))
                            {
                                $info["name"] = explode("@", $val['email_address'])[0];
                            } else {
                                $info["name"] = implode(" ", $name);
                            }
                            $user = get_user_by('email', $val['email_address']);
                            $phone = get_user_meta($user->ID, 'billing_phone', true);

                            if (!empty($phone)) { $info["phone"] = $phone; }

                            Api::send("add_subscriber", $info);
                            \Mktr\Tracker\Logs::debug($info, 'save_order_add_subscriber');

                            $check[$s->email] = $time;
                        }

                        if (Api::getStatus() != 200) {
                            $allGood = false;
                        }
                    }
                }
            }

            Config::session()->set('emailSend', $check);
            
            if ($allGood)
            {
                Config::session()->set('saveOrder', array());
            }
        }
        // return '/* TheMaketer */ console.log('.(int) $allGood.','.json_encode(Api::getInfo(), true).');';
        //return '/* TheMaketer */ console.log('.(int) $allGood.','.json_encode([ Api::getInfo() ], true).');';
        return json_encode([(int) $allGood, Api::getInfo() ], true);
    }
}