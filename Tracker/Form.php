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

class Form
{
    private static $form_fields = array();
    private static $init = null;

    const defFields = array(
        'title' => '',
        'type' => 'text',
        'default' => '',
        'description' => '',
        'holder' => '',
        'options' => array(
            array('value' => 0, 'label' => "Disable"),
            array('value' => 1, 'label' => "Enable")
        )
    );

    public static function init()
    {
        if (self::$init == null) {
            self::$init = new self();
        }

        return self::$init;
    }

    public static function formFields($fields)
    {
        foreach ($fields as $key=>$value) {
            $fields[$key] = array_merge(self::defFields, $value);
        }

        self::$form_fields = array_merge(self::$form_fields, $fields);

        return self::init();
    }

    public static function initProcess()
    {
        $data = Config::POST(Config::$name);
        
        if (!empty($data)) {
            $fail = array('tracking' => false, 'google' => false);
            $onboarding = false;
            $push_status = true;
            foreach ($data[Config::$name] as $key=>$value) {

                if (in_array($key, array('tracking_key', 'rest_key', 'customer_id')) && empty($value)) { $fail['tracking'] = true; }
                if (in_array($key, array('google_tagCode')) && empty($value) && isset($data[Config::$name]['google_status']) && $data[Config::$name]['google_status'] == 1) { $fail['google'] = true; }
				if (in_array($key, array('update_feed', 'update_review')) && empty($value)) { $value = 4; }
                if (in_array($key, array('allow_export_gravity_data'))) { $value = serialize($data[Config::$name]['allow_export_gravity_data']); }
                if ($key !== 'onboarding') {
                    Config::setValue($key, $value);
                } else {
                    $onboarding = array($key, $value);
                }
                
                if ($key == 'push_status') { Observer::pushStatus(); $push_status = false; }
                if ($key == 'opt_in') {
                    $plug = 'mailpoet/mailpoet.php';
                    if ( ! function_exists( 'is_plugin_active' ) ) {
                        require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
                    }
                    $active    = \is_plugin_active($plug);
                    if ($active) {
                        $stat = \MailPoet\Settings\SettingsController::getInstance()->get('signup_confirmation.enabled');
                        
                        if (Config::getValue('opt_in_oldmail') === null || $stat != 0) {
                            Config::setValue('opt_in_oldmail', $stat);
                        }
                        
                        if ($value == 1) {
                            \MailPoet\Settings\SettingsController::getInstance()->set('signup_confirmation.enabled', 0);
                        } else {
                            \MailPoet\Settings\SettingsController::getInstance()->set('signup_confirmation.enabled', Config::getValue('opt_in_oldmail'));
                        }
                        $id = Config::getMailPoetId(false);
                        
                        if ( $id !== null ) {
                            $optin_on_checkout = \MailPoet\Settings\SettingsController::getInstance()->get('woocommerce.optin_on_checkout');
                            if ($optin_on_checkout !== null) {
                                if (!array_key_exists('segments', $optin_on_checkout)) {
                                    $optin_on_checkout['segments'] = [ $id ];
                                } else if (!in_array($id, $optin_on_checkout['segments'])) {
                                    $optin_on_checkout['segments'][] = $id;
                                }
                                if ($value == 1) { $optin_on_checkout['enabled'] = 1; }
                            } else if ($value == 1) {
                                $optin_on_checkout = [
                                    'message' => 'I would like to receive exclusive emails with discounts and product information',
                                    'segments' => [ $id ],
                                    'enabled' => 1
                                ];
                            }
                            if ($optin_on_checkout !== null) {
                                \MailPoet\Settings\SettingsController::getInstance()->set('woocommerce.optin_on_checkout', $optin_on_checkout);
                            }

                            $subscribe = \MailPoet\Settings\SettingsController::getInstance()->get('subscribe');
                            $list = ['on_register', 'on_comment'];
                            if ($subscribe !== null) {
                                foreach($list as $k) {
                                    if (array_key_exists($k, $subscribe)) {
                                        if (!array_key_exists('segments', $subscribe[$k])) {
                                            $subscribe[$k]['segments'] = [ $id ];
                                        } else if (!in_array($id, $subscribe[$k]['segments'])) {
                                            $subscribe[$k]['segments'][] = $id;
                                        }
                                        if ($value == 1) { $subscribe[$k]['enabled'] = 1; }
                                    } else if ($value == 1) {
                                        $subscribe[$k] = [
                                            'segments' => [ $id ],
                                            'label' => 'I would like to receive exclusive emails with discounts and product information',
                                            'enabled' => 1
                                        ];
                                    }
                                }
                            } else if ($value == 1) {
                                foreach($list as $k) {
                                    $subscribe[$k] = [
                                        'segments' => [ $id ],
                                        'label' => 'I would like to receive exclusive emails with discounts and product information',
                                        'enabled' => 1
                                    ];
                                }
                            }

                            if ($subscribe !== null) {
                                \MailPoet\Settings\SettingsController::getInstance()->set('subscribe', $subscribe);
                            }

                            $f = \MailPoet\DI\ContainerWrapper::getInstance()->get(\MailPoet\Form\FormsRepository::class);
                            
                            foreach ($f->findAll() as $ff) {
                                $settings = $ff->getSettings();
                                if (!in_array($id, $settings['segments'])) {
                                    $settings['segments'][] = (string) $id; $ff->setSettings($settings);
                                    try {
                                        $f->persist($ff); $f->flush();
                                    } catch (\Exception $e) {
                                        //var_dump($e); die();
                                    }
                                }
                            }
                        }
                    }
                }
            }
            
            if ($push_status) {
                Observer::pushStatus();
            }
            if ( ! function_exists( 'is_plugin_active' ) ) {
                require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
            }
            $active = \is_plugin_active('translatepress-multilingual/index.php');
            if ($active) {
                Config::setValue('translate_press', 1);
            } else {
                Config::setValue('translate_press', 0);
            }

            if ($fail['tracking'] && ( Config::getValue('status') == 1 || $onboarding[1] != 2 )) {
                Admin::addNotice( array( 'type' => 'error', 'message'=> 'To enable tracking you need to fill all required (*) fields' ) );
            } else {
                if ($onboarding !== false && $onboarding[1] != 2) {
                    if ($onboarding[1] == 0) {
                        Config::setValue('status', 1);
                        if (empty(Config::getValue('google_tagCode'))) {
                            Config::setValue('google_status', 0);
                        } else {
                            Config::setValue('google_status', 1);
                        }
                    } else {
                        if (empty(Config::getValue('google_tagCode'))) {
                            Config::setValue('google_status', 0);
                        }
                    }
                    $onboarding[1]++;
                    Config::setValue($onboarding[0], $onboarding[1]);
                } else if ($fail['google']) {
                    if (empty(Config::getValue('google_tagCode'))) {
                        Config::setValue('google_status', 0);
                    }
                    Admin::addNotice( array( 'type' => 'error', 'message'=> 'To enable "Google Tag Manager" you need to add your ID' ) );
                } else {
                    Admin::addNotice( array( 'type' => 'succes', 'message'=>'Your settings have been saved.' ) );
                }
            }
            \Mktr\Tracker\Routes\refreshJS::execute(false);
        }
    }

    public static function clean()
    {
        self::$form_fields = array();
    }
}
