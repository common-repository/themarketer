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

class Admin
{
    private static $init = null;

    private static $notice = array();
    private static $rate_active = null;
    private static $defOptions = array(
        array('value' => 0, 'label' => "Disable"),
        array('value' => 1, 'label' => "Enable")
    );

    private static $inputs = array(
        'status' => array(
            'label' => 'Tracking Status:',
            'tag' => 'input',
            'type' => 'checkbox',
            'name' => 'status',
            'placeholder' => null
        ),
        'tracking_key' => array(
            'label' => 'Tracking API Key *',
            'tag' => 'input',
            'type' => 'text',
            'name' => 'tracking_key',
            'placeholder' => 'Your Tracking API Key'
        ),
        'rest_key' => array(
            'label' => 'Rest API Key *',
            'tag' => 'input',
            'type' => 'text',
            'name' => 'rest_key',
            'placeholder' => 'Your REST API Key'
        ),
        'customer_id' => array(
            'label' => 'Customer ID *',
            'tag' => 'input',
            'type' => 'text',
            'name' => 'customer_id',
            'placeholder' => 'Your Customer ID'
        ),
        /* Cron Settings */
        'cron_feed' => array(
            'label' => 'Activate Cron Feed',
            'tag' => 'input',
            'type' => 'checkbox',
            'name' => 'cron_feed',
            'placeholder' => null
        ),
        'update_feed' => array(
            'label' => 'Cron Update feed(hours)',
            'tag' => 'input',
            'type' => 'text',
            'name' => 'update_feed',
            'description' => 'Set number of hours',
            'placeholder' => null
        ),
        'cron_review' => array(
            'label' => 'Activate Cron Review',
            'tag' => 'input',
            'type' => 'checkbox',
            'name' => 'cron_review',
            'placeholder' => null
        ),
        'update_review' => array(
            'label' => 'Cron Update Review(hours)',
            'tag' => 'input',
            'type' => 'text',
            'name' => 'update_review',
            'description' => 'Set number of hours',
            'placeholder' => null
        ),
        /* Extra Settings */
        'opt_in' => array(
            'label' => 'Double opt-in setting',
            'tag' => 'input',
            'type' => 'checkbox-optin',
            'description' => 'Choose if Double opt-in will be handled by theMarketer<br />Works only with MailPoet Plugin',
            'name' => 'opt_in',
            'placeholder' => null
        ),
        'push_status' => array(
            'label' => 'Push Notification',
            'tag' => 'input',
            'type' => 'checkbox',
            'name' => 'push_status',
            'placeholder' => null
        ),
        'default_stock' => array(
            'label' => 'Default Stock',
            'tag' => 'select',
            'type' => 'select',
            'name' => 'default_stock',
            'description' => 'Status if stock is negative like "-1"',
            'options' => array(
                array('value' => 0, 'label' => 'Out of Stock'),
                array('value' => 1, 'label' => 'In Stock'),
                array('value' => 2, 'label' => 'In supplier stock')
            ),
            'placeholder' => null
        ),
        'allow_export_gravity' => array(
            'label' => 'Export Gravity Form entries',
            'tag' => 'input',
            'type' => 'checkbox',
            'name' => 'allow_export_gravity',
            'placeholder' => null
        ),
        'allow_export_gravity_all' => array(
            'label' => 'Gravity Form All',
            'tag' => 'input',
            'type' => 'checkbox',
            'name' => 'allow_export_gravity_all',
            'placeholder' => null
        ),
        'allow_export_gravity_subscribe' => array(
            'label' => 'Contact status',
            'tag' => 'input',
            'type' => 'checkbox-sub',
            'description' => 'Choose Contact status',
            'name' => 'allow_export_gravity_subscribe',
            'placeholder' => null
        ),
        'allow_export_gravity_tag' => array(
            'label' => 'Tag',
            'tag' => 'input',
            'type' => 'text',
            'name' => 'allow_export_gravity_tag',
            'placeholder' => null
        ),
        'allow_export' => array(
            'label' => 'Allow orders export',
            'tag' => 'input',
            'type' => 'checkbox',
            'name' => 'allow_export',
            'placeholder' => null
        ),
        'add_description' => array(
            'label' => 'Add Description',
            'tag' => 'input',
            'type' => 'checkbox',
            'name' => 'add_description',
            'placeholder' => null
        ),
        'selectors' => array(
            'label' => 'Trigger Selectors',
            'tag' => 'input',
            'type' => 'text',
            'name' => 'selectors',
            'description' => 'Selectors that will trigger events Like AddToCart',
            'placeholder' => null
        ),
        /* Attr Settings */
        'brand' => array(
            'label' => 'Brand Attribute',
            'tag' => 'input',
            'type' => 'text',
            'name' => 'brand',
            'description' => 'use "|" to separate Example: brand|manufacturer',
            'placeholder' => null
        ),
        'color' => array(
            'label' => 'Color Attribute',
            'tag' => 'input',
            'type' => 'text',
            'name' => 'color',
            'description' => 'use "|" to separate Example: brand|manufacturer',
            'placeholder' => null
        ),
        'size' => array(
            'label' => 'Size Attribute',
            'tag' => 'input',
            'type' => 'text',
            'name' => 'size',
            'description' => 'use "|" to separate Example: brand|manufacturer',
            'placeholder' => null
        ),
        /* Google */
        'google_status' => array(
            'label' => 'Google Tag Manager',
            'tag' => 'select',
            'type' => 'checkbox',
            'name' => 'google_status',
            'placeholder' => null
        ),
        'google_tagCode' => array(
            'label' => 'Google Tag Manager ID',
            'tag' => 'input',
            'type' => 'text',
            'name' => 'google_tagCode',
            'placeholder' => null
        ),
        'onboarding' => array(
            'tag' => 'input',
            'name' => 'onboarding',
            'type' => 'hidden',
            'placeholder' => null
        )
    );

    public static function init()
    {
        if (self::$init == null) {
            self::$init = new self();
        }
        return self::$init;
    }

    public static function loadAdmin()
    {
        Form::initProcess();

        if (Config::getOnboarding() === 2 && Config::getStatus() === 1 && (Config::getCronFeed() === 1 || Config::getCronReview() === 1)) {
            if (!wp_next_scheduled('MKTR_CRON')) {
                wp_schedule_event(time(), 'hourly', 'MKTR_CRON');
            }
        }
        add_filter('plugin_action_links_'.Config::getPluginBase(), array(self::init(), 'action_links'));
        add_filter('plugin_row_meta', array(self::init(), 'extra_links'), 10, 2);
        add_action('admin_menu', array(self::init(), 'menu'));
        add_action('admin_notices', array(self::init(), 'notice'));
        // add_filter('wp_admin_notice_args', array(self::init(), 'noticeFilter'), 10, 2 );
        // add_filter('browse-happy-notice', array(self::init(), 'noticeFilter'), 10, 2 );
        // add_action('all_admin_notices', array(self::init(), 'noticeFilter'));
        add_action('woocommerce_order_edit_status', array(Observer::init(), 'orderUp'), 10, 2);
        add_action('admin_footer', array(self::init(), 'feedback'));
        add_action('admin_enqueue_scripts', array(self::init(), 'scripts'));
    }
    public static function gform_menu($setting_tabs, $form_id)
    {
        foreach ($setting_tabs as $key => $v) {
            if ($v['name'] == 'mktr') {
                unset($setting_tabs[$key]);
            }
        }
        return $setting_tabs;
    }
    
    public static function addNotice($notice)
    {
        self::$notice[] = is_array($notice) ? $notice : array('message' => $notice);
        return self::init();
    }
    public static function feedback($notice = null)
    {
        $d = self::deactivation();
        if ($d[1]) {
            $content = array();
            /*
            $list = array('I’m not sending email campaigns right now', 'It didn’t have the features I want', 'I didn’t like the email editor',
			'It was too confusing', 'There were technical issues', 'I don’t have enough email contacts', 'It’s a temporary deactivation');
            foreach($list as $r) {
                $content[] = array(
                    'label' => $r,
                    'tag' => 'input',
                    'type' => 'radio',
                    'name' => 'deactivation_option',
                    'description' => null,
                    'placeholder' => null
                );
            }*/
            $content[] = "Your feedback is important to us as we're constantly working on improving our customer experience.";
            $content[] = array(
                'label' => 'Message',
                'tag' => 'textarea',
                'type' => 'textarea',
                'name' => 'message',
                'description' => null,//"Don't Worry this feedback is Anonim",
                'placeholder' => "I'm deactivating this plugin because..."
            );
            $form = array(
                array(
                    "type" => "body",
                    "title-h" => "h2",
                    "title" => "Can you tell us why are you leaving already?",
                    "content" => $content
                ),
                array(
                    "type" => "footer",
                    "content" => array(
                        '<div class="mktr-content-left"><button type="button" class="mktr-button none-close mktr-modal-feedback-deactivate">Close and deactivate <span class="icon mktr-dis"></span></button></div>',
                        '<div class="mktr-content-right"><button type="button" class="mktr-button mktr-modal-feedback-submit">Submit Feedback <span class="icon mktr-feedback"></span></button></div><br class="mktr-space"/>',
                        '<button type="button" class="mktr-button-close mktr-modal-feedback-close"><span class="icon mktr-close swg-dark"></span></button>'
                    )
                )
            );
    
            echo '<div class="mktr-modal mktr-modal-feedback"><div class="mktr-modal-body">'. self::gForm($form, false) .'</div></div>';
        }
    }

    private static function deactivation()
    {
		if (function_exists('get_current_screen') ) {
            $screen = get_current_screen();
            
            if ( !is_null($screen) ) {
                return [ preg_match('(themarketer|admin_page_mktr_gravity)', $screen->id) === 1, preg_match('(plugins)', $screen->id) === 1];
            }
        }
		return [false, false];
	}

    public function scripts() {
        $d = self::deactivation();
        if ($d[1] || self::rateActive()) {
            wp_enqueue_style('mktr-survey', Run::plug_url('/assets/deactivation.css'));
            wp_enqueue_script('mktr-survey', Run::plug_url('/assets/deactivation.js'));
            wp_localize_script('mktr-survey', 'mktr_data', array( 'url' => Config::getBaseURL() ));
        }
        if ($d[0]) {
            wp_enqueue_style('mktr-admin', Run::plug_url('/assets/style.css'));
        }
	}
/*
    public static function noticeFilter($notice = null, $response = null){
        //var_dump(func_get_args());
        return $notice;
    }
*/
    public static function rateActive()
    {
        if (self::$rate_active === null) {
            self::$rate_active = Config::getRated() == 0 && Config::getRatedInstall() < time();
        }
        return self::$rate_active;
    }

    public static function notice()
    {
        if (Config::getValue("redirect") == 1) {
            if (Config::getRatedInstall() == 0) {
                Config::setValue("rated_install", time()+1209600);
            }
            Config::setValue("redirect", 0);
            \wp_redirect(\admin_url('admin.php?page=mktr_tracker'));
        }
        $out = array();
        if (Config::getStatus() == 1 && empty(Config::getKey()) || Config::getOnboarding() !== 2) {
            $out[] = '<div class="updated notice notice-success is-dismissible"><p>theMarketer is almost ready. <a href="'.admin_url('admin.php?page=mktr_tracker').'">Click Here</a></p></div>';
        }
        if (self::rateActive()) {
            $out[] = '<div class="mktr-modal mktr-modal-rate">
    <div class="mktr-modal-body">
        <div class="mktr-head"><img src="' . Run::plug_url('/assets/logo.png') . '"></div>
        <div class="mktr-content">
            <div class="mktr-content-body">
                <div class="mktr-content-text">
                    <h2>How would you rate your experience so far with theMarketing?</h2>
                    <div class="mktr-content-field">
                        <label class="mktr-content-label">On a scale from 1 to 5</label>
                        <div class="mktr-content-input">
                        <input type="hidden" id="mktr-rating-value" value="0">
                        <div class="mktr-rating"><span rate="1">☆</span><span rate="2">☆</span><span rate="3">☆</span><span rate="4">☆</span><span rate="5">☆</span></div>
                        </div>
                    </div>
                    <div class="mktr-content-field mktr-modal-rate-message" style="display:none">
                        <label for="mktr-rating-message" class="mktr-content-label">Message</label>
                        <div class="mktr-content-input">
                            <textarea id="mktr-rating-message" rows="2" cols="100" placeholder="We would love to hear your feedback on how we can make things better."></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mktr-content-footer">
                <div class="mktr-content-right">
                    <button type="button" class="mktr-button mktr-modal-rate-submit">Submit Feedback <span class="icon mktr-feedback"></span></button>
                </div>
            </div>
            <button type="button" class="mktr-button-close mktr-modal-rate-close"><span class="icon mktr-close swg-dark"></span></button>
        </div>
    </div>
</div>';

            self::$notice[] = array(
                'type' => 'info',
                'class' => 'mktr-modal-rate-active',
                'dismissible' => false,
                'message' => 'We would love to hear your opinion, How are things going with theMarketer? Click HERE!'
            );
        }

        if (!empty(self::$notice)) {
            /* notice-error | notice-success | notice-fail | notice-info*/
            foreach (self::$notice as $value) {
                $class = array(
                    'notice',
                    'notice-'.(isset($value['type']) ? $value['type'] : 'success')
                );
                if (isset($value['dismissible']) && $value['dismissible']) {
                    $class[] = 'is-dismissible';
                }
                if (!empty($value['class'])) {
                    $class[] = $value['class'];
                }
                $out[] = '<div class="' . implode(' ', $class) . '"><p>' . esc_html($value['message']) . '</p></div>';
            }
        }
        echo implode(PHP_EOL, $out);
    }

    public static function action_links($links)
    {
        $link = array();
        $link['settings'] = '<a href="'. admin_url('admin.php?page=mktr_tracker') . '" target="_blank"> Settings</a>';
        if (Config::getOnboarding() !== 2) {
            $link['onboarding'] = '<a href="'. admin_url('admin.php?page=mktr_tracker') . '"> Start Onboarding</a>';
        }
        return array_merge($link, $links);
    }

    public static function extra_links($links, $file)
    {
        if (Config::getPluginBase() !== $file) {
            return $links;
        }

        // $links['settings'] = '<a href="'. admin_url('admin.php?page=mktr_tracker') . '"> Settings</a>';

        foreach ($links as $k=>$v) {
            $links[$k] = str_replace('">', '" target="_blank">', $v);
        }

        return $links;
    }

    public static function menu()
    {
        add_menu_page(
            'TheMarketer',
            'TheMarketer',
            null,
            'mktr',
            null,
            Config::getSVG(),
            7
        );
        //Config::setValue("onboarding", 0);
        //eDebug( Config::getOnboarding(), Config::getOnboarding() !== 2);
        if (Config::getOnboarding() !== 2) {
            add_submenu_page(
                'mktr',
                'Start Onboarding',
                'Start Onboarding',
                'manage_options',
                'mktr_tracker',
                array(self::init(), 'onboarding')
            );
        } else {
            add_submenu_page(
                'mktr',
                'Tracker',
                'Tracker',
                'manage_options',
                'mktr_tracker',
                array(self::init(), 'tracker')
            );
    
            add_submenu_page(
                'mktr',
                'Google',
                'Google',
                'manage_options',
                'mktr_google',
                array(self::init(), 'google')
            );
            /*
            $plug = 'gravityforms/gravityforms.php';
            $active = \is_plugin_active($plug);
            if ($active) {
                add_submenu_page(
                    'mktr',
                    'Gravity Form',
                    'Gravity Form',
                    'manage_options',
                    'gf_settings&subview=mktr',
                    array(self::init(), 'gravity')
                );

                if (Config::getAllowExportGravity()) {
                    add_submenu_page(
                        'options.php',
                        'Gravity Form entries',
                        'Gravity Form entries',
                        'manage_options',
                        'mktr_gravity',
                        array(self::init(), 'gravity')
                    );
                }
            }
            */
        }
    }
    
    public static function gForm($fields = array(), $getv = true){
        $content = '<form method="POST" action="" enctype="multipart/form-data">';
        $content .= '<div class="mktr-head"><img src="'.Run::plug_url('/assets/logo.png').'"></div>';
        $content .= '<div class="mktr-content">';

        foreach($fields as $k => $v) {
            if ($v['type'] === 'notice') {
                $content .= '<div class="mktr-content-head">';
                if (isset($v['title'])) {
                    $content .= '<h1>'.$v['title'].'</h1>';
                }
                foreach ($v['content'] as $data) {
                    $class = array(
                        'mktr-content-notice',
                        (isset($data['type']) ? $data['type'] : 'success')
                    );
                    if (!empty($data['class'])) {
                        $class[] = $data['class'];
                    }
                    $content .= '<div class="'.implode(' ', $class).'">'.$data['message'].'</div>';
                }
                $content .= '</div>';
            } else if ($v['type'] === 'head') {
                $content .= '<div class="mktr-content-head"><h1>'.$v['title'].'</h1>'.implode("<br />", $v['content']).'</div>';
            } else if ($v['type'] === 'body') {
                $h = isset($v["title-h"]) ? $v["title-h"] : 'h1';
                $content .= '<div class="mktr-content-body"><div class="mktr-content-text"><'.$h.'>'.$v['title'].'</'.$h.'>';
                foreach($v['content'] as $kk => $c) {
                    if (is_array($c)) {
                        if ($getv) {
                            if ($c['name'] === 'allow_export_gravity_data') {
                                $vv = Config::getValue($c['name']);
                                $v = unserialize($vv);
                                $value = $c['def'];
                                if (isset($c['extra']) && isset($v[$c['extra'][0]][$c['extra'][1]])) {
                                    $value = $v[$c['extra'][0]][$c['extra'][1]];
                                }
                            } else {
                                $value = Config::getValue($c['name']);
                            }
                        } else {
                            $value = '';
                        }
                        if (is_array($value)) { $value = implode('|', $value); }

                        if (!in_array($c['type'], array('radio','hidden'))) {
                            $inputData = "";
                            if ($c['type'] === 'checkbox') {
                                if ($c['name'] === 'allow_export_gravity_data' && isset($c['extra'])) {
                                    $fid = 'mk_check_' . $c['name'].'_'.$c['extra'][0].'_'.$c['extra'][1];
                                    $name = $c['name'].']['.$c['extra'][0].']['.$c['extra'][1];
                                } else {
                                    $fid = 'mk_check_' . $c['name'];
                                    $name = $c['name'];
                                }
                                $inputData .= '<input type="hidden" id="' . $fid . '_value" name="'.Config::$name.'['.$name.']" value="' . (int) $value .'">';
                                $inputData .= '<input id="' . $fid .'" class="mkcheck" type="checkbox" onchange="document.querySelector(\'#' . $fid .
                                '_value\').value=this.checked ? 1 : 0; this.innerHTML = this.checked ? \'Active\':\'Inactive\'" ' . ((int) $value === 1 ? 'checked' : '') .
                                '/><label class="mk-btn" for="' . $fid .'" ></label>';
                            } else if ($c['type'] === 'checkbox-optin') {
                                $inputData .= '<input type="hidden" id="mk_check_' . $c['name'] . '_value" name="'.Config::$name.'['.$c['name'].']" value="' . (int) $value .'">';
                                $inputData .= '<input id="mk_check_'. $c['name'] .'" class="mkcheck" type="checkbox" onchange="document.querySelector(\'#mk_check_' . $c['name'] .
                                '_value\').value=this.checked ? 1 : 0; this.innerHTML = this.checked ? \'Active\':\'Inactive\'" ' . ((int) $value === 1 ? 'checked' : '') .
                                '/><label class="mk-btn-opt" for="mk_check_'. $c['name'] .'" ></label>';
                            }  else if ($c['type'] === 'checkbox-sub') {
                                
                                if ($c['name'] === 'allow_export_gravity_data' && isset($c['extra'])) {
                                    $fid = 'mk_check_' . $c['name'].'_'.$c['extra'][0].'_'.$c['extra'][1];
                                    $name = $c['name'].']['.$c['extra'][0].']['.$c['extra'][1];
                                } else {
                                    $fid = 'mk_check_' . $c['name'];
                                    $name = $c['name'];
                                }

                                $inputData .= '<input type="hidden" id="' . $fid . '_value" name="'.Config::$name.'['.$name.']" value="' . (int) $value .'">';
                                $inputData .= '<input id="' . $fid .'" class="mkcheck" type="checkbox" onchange="document.querySelector(\'#' . $fid .
                                '_value\').value=this.checked ? 1 : 0; this.innerHTML = this.checked ? \'Active\':\'Inactive\'" ' . ((int) $value === 1 ? 'checked' : '') .
                                '/><label class="mk-btn-sub" for="' . $fid .'" ></label>';
                            } else if ($c['type'] === 'select') {
                                $inputData .= '<select id="'.Config::$name.'_'.$c['name'].'" name="'.Config::$name.'['.$c['name'].']" '.( empty($value) ? "" : ' value="'.$value.'"' ).'>';
                                if (empty($c['options'])) { $c['options'] = self::$defOptions; }
                                foreach($c['options'] as $o) {
                                    $inputData .= '<option value="'.$o['value'].'" '.($value == $o['value'] ? 'selected="selected" ' : '').'>'.$o['label'].'</option>';
                                }
                                $inputData .= '</select>';
                            } else if ($c['type'] === 'textarea') {
                                $inputData .= '<'.$c['tag'].
                                ' id="'.Config::$name.'_'.$c['name'].
                                '" rows="2" cols="100" name="'.Config::$name.'['.$c['name'].']" placeholder="'.(isset($c['placeholder']) ? $c['placeholder'] : $c['label']).
                                '" '.( empty($value) ? "" : ' value="'.$value.'"' ).'></'.$c['tag'].'>';
                            } else {
                                if ($c['name'] === 'allow_export_gravity_data' && isset($c['extra'])) {
                                    $fid = 'mk_check_' . $c['name'].'_'.$c['extra'][0].'_'.$c['extra'][1];
                                    $name = $c['name'].']['.$c['extra'][0].']['.$c['extra'][1];
                                } else {
                                    $fid = 'mk_check_' . $c['name'];
                                    $name = $c['name'];
                                }
                                $inputData .= '<'.$c['tag'].
                                ' type="'.$c['type'].'" id="'.$fid.
                                '" name="'.Config::$name.'['.$name.']" placeholder="'.(isset($c['placeholder']) ? $c['placeholder'] : $c['label']).
                                '" '.( empty($value) ? "" : ' value="'.$value.'"' ).'/>';
                            }
                            // '.Config::$name.'_'.$c['name'].' 
                            $content .= '<div class="mktr-content-field">';
                            $content .= '<label for="'.$fid.'" class="mktr-content-label">'.$c['label'].'</label>';
                            $content .= '<div class="mktr-content-input">';
                            $content .= $inputData;
                            if (!empty($c['description'])) {
                                $content .= '<p class="description">'.$c['description'].'</p>';
                            }
                            $content .= '</div>';    
                            $content .= '</div>';
                        } else {
                            if ($c['type'] === 'radio') {

                                $content .= '<div class="mktr-content-field">';
                                $content .= '<div class="mktr-content-radio">';
                                $content .= '<'.$c['tag'].' type="'.$c['type'].'" id="'.Config::$name.'_'.$kk.$c['name'].'" name="'.Config::$name.'['.$c['name'].']" placeholder="'.
                                (isset($c['placeholder']) ? $c['placeholder'] : $c['label']).'" '.( empty($value) ? "" : ' value="'.$value.'"' ).'/>';
                                $content .= '<label for="'.Config::$name.'_'.$kk.$c['name'].'" class="mktr-content-label">'.$c['label'].'</label>';
                                $content .= '</div>';
                                $content .= '</div>';
                            } else {
                                if ($c['name'] === 'allow_export_gravity_data' && isset($c['extra'])) {
                                    $name = $c['name'].']['.$c['extra'][0].']['.$c['extra'][1];
                                } else {
                                    $name = $c['name'];
                                }
                                $content .= '<input type="hidden" name="'.Config::$name.'['.$name.']" value="' . $value .'">';
                            }
                        }
                    } else {
                        $content .= ($kk === 0 ? "" : "<br />"). $c;
                    }
                }
                $content .= '</div></div>';
            } else if ($v['type'] === 'hidden') {
                $c = $v['content'];
                $value = Config::getValue($c['name']);
                $content .= '<'.$c['tag'].' type="'.$c['type'].'" type="hidden" name="'.Config::$name.'['.$c['name'].']" value="'.$value.'"/>';
            } else if ($v['type'] === 'footer') {
                $content .= '<div class="mktr-content-footer">';
                $content .= implode(PHP_EOL, $v['content']);
                $content .= '</div>';
            }
        }

        $content .= '</div><input type="hidden" id="'.Config::$name.'" name="'.Config::$name.'[valid]" value="'.Config::$name.'_valid" /></form>';
        return $content;
    }
    public static function onboarding(){
        if (isset($_GET['back'])) {
            Config::setValue('onboarding', 0);
            \wp_redirect(\admin_url('admin.php?page=mktr_tracker'));
        }
        $forms = array(array(),array());
        if (!empty(self::$notice)) {
            $bk = self::$notice;
            self::$notice = array();
            foreach($bk as $notice) {
                if ($notice['type'] === 'error') {
                    self::$notice[] = $notice;
                }
            }
            if (!empty(self::$notice)) {
                $forms[Config::getValue('onboarding')][] = array(
                    "type" => "notice",
                    "content" => self::$notice
                );
            }
        }
        $forms[0][] = array(
            "type" => "body",
            "title" => "Let's get started",
            "content" => array(
                "Please create an account or log in if you have one already.",
                '<a class="mktr-button" href="https://app.themarketer.com/register?utm_campaign=woo_plugin" target="_blank">Create account <span class="icon mktr-arrow-right"></span></a>'
            )
        );
        $forms[0][] = array(
            "type" => "body",
            "title" => "Main settings",
            "content" => array(
                "In your theMarketer account, head to Settings > Technical integration.",
                "Copy your API keys and paste them in this section to complete your integration.",
                self::$inputs['tracking_key'],
                self::$inputs['rest_key'],
                self::$inputs['customer_id']
            )
        );
        $forms[0][] = array(
            "type" => "body",
            "title" => "Setup Google Tag Manager",
            "content" => array(
                "To integrate theMarketer with your store, you need to have Google Tag Manager (GTM) installed on your website.",
                "Copy your GTM ID (container ID) and paste it in this section.",
                self::$inputs['google_tagCode']
            )
        );
        $forms[0][] = array(
            "type" => "hidden",
            "content" => self::$inputs['onboarding']
        );
        $forms[0][] = array(
            "type" => "footer",
            "content" => array(
                '<div class="mktr-content-left">Need help? <a href="https://themarketer.com/integrations/woocommerce" target="_blank"> Visit our help article <span class="icon mktr-export-2"></span></a></div>',
                '<div class="mktr-content-right"><button type="submit" class="mktr-button">Continue <span class="icon mktr-arrow-right"></span></button></div><br class="mktr-space"/>'
            )
        );

        $forms[1][] = array(
            "type" => "head",
            "title" => "Let’s make sure that everything is set up correctly",
            "content" => array(
                "Please double check that all information is correct.",
                "Once you've done this, enable the integration using these dropdown menus.",
                "You can always return here and disable them.",
            )
        );
        $c = array(
            self::$inputs['status'],
            self::$inputs['google_status']
        );
        /*
        $plug = 'gravityforms/gravityforms.php';
        $active = \is_plugin_active($plug);

        if ($active) {
            $c[] = self::$inputs['allow_export_gravity'];
        }

        if (!$active && Config::getAllowExportGravity()) { 
            Config::setValue('allow_export_gravity', 0);
        }
        */
        
        $forms[1][] = array(
            "type" => "body",
            "title" => "Components status:",
            "content" => $c
        );

        $forms[1][] = array(
            "type" => "hidden",
            "content" => self::$inputs['onboarding']
        );
        $forms[1][] = array(
            "type" => "footer",
            "content" => array(
                '<div class="mktr-content-left">Need help? <a href="https://themarketer.com/integrations/woocommerce" target="_blank"> Visit our help article <span class="icon mktr-export-2"></span></a></div>',
                '<div class="mktr-content-right"><a href="'.\admin_url('admin.php?page=mktr_tracker&back').'" class="mktr-button none"><span class="icon mktr-arrow-right"></span>Back<span class="icon mktr-arrow-right"></span></a>&emsp;<button type="submit" class="mktr-button">Finish setup <span class="icon mktr-arrow-right"></span></button></div><br class="mktr-space"/>'
            )
        );
        
        echo self::gForm($forms[Config::getValue('onboarding')]);
    }

    public static function google()
    {
        $form = array();
        
        if (!empty(self::$notice)) {
            $form[] = array(
                "type" => "notice",
                "content" => self::$notice
            );
        }

        $form[] = array(
            "type" => "body",
            "title" => "Google Main Settings",
            "content" => array(
                self::$inputs['google_status'],
                self::$inputs['google_tagCode']
            )
        );
        $form[] = array(
            "type" => "hidden",
            "content" => self::$inputs['onboarding']
        );
        $form[] = array(
            "type" => "footer",
            "content" => array(
                '<div class="mktr-content-left">Need help? <a href="https://themarketer.com/integrations/woocommerce" target="_blank"> Visit our help article <span class="icon mktr-export"></span></a></div>',
                '<div class="mktr-content-right"><button type="submit" class="mktr-button">Save changes <span class="icon mktr-save"></span></button></div><br class="mktr-space"/>'
            )
        );
        echo self::gForm($form);
    }

    public static function tracker()
    {
        $form = array();
        
        if (!empty(self::$notice)) {
            $form[] = array(
                "type" => "notice",
                "content" => self::$notice
            );
        }

        $form[] = array(
            "type" => "body",
            "title" => "Main Settings",
            "content" => array(
                self::$inputs['status'],
                self::$inputs['tracking_key'],
                self::$inputs['rest_key'],
                self::$inputs['customer_id']
            )
        );
        $form[] = array(
            "type" => "body",
            "title" => "Cron Settings",
            "content" => array(
                "This zone is dedicated to activate and set Cron Updates",
                self::$inputs['cron_feed'],
                self::$inputs['update_feed'],
                self::$inputs['cron_review'],
                self::$inputs['update_review']
            )
        );
        $c = array(
            self::$inputs['opt_in'],
            self::$inputs['add_description'],
            self::$inputs['push_status'],
            self::$inputs['allow_export'],
            self::$inputs['default_stock'],
            self::$inputs['selectors']
        );
        /*
        $plug = 'gravityforms/gravityforms.php';
        $active = \is_plugin_active($plug);

        if ($active) {
            $c[] = self::$inputs['allow_export_gravity'];
        }

        if (!$active && Config::getAllowExportGravity()) { 
            Config::setValue('allow_export_gravity', 0);
        }
        */
        $form[] = array(
            "type" => "body",
            "title" => "Extra Settings",
            "content" => $c
        );
        $form[] = array(
            "type" => "body",
            "title" => "Attribute Settings",
            "content" => array(
                self::$inputs['brand'],
                self::$inputs['color'],
                self::$inputs['size']
            )
        );
        $form[] = array(
            "type" => "hidden",
            "content" => self::$inputs['onboarding']
        );
        $form[] = array(
            "type" => "footer",
            "content" => array(
                '<div class="mktr-content-left">Need help? <a href="https://themarketer.com/integrations/woocommerce" target="_blank"> Visit our help article <span class="icon mktr-export"></span></a></div>',
                '<div class="mktr-content-right"><button type="submit" class="mktr-button">Save changes <span class="icon mktr-save"></span></button></div><br class="mktr-space"/>'
            )
        );
        
        echo self::gForm($form);
    }

    public static function gravity()
    {
        $form = array();
        
        if (!empty(self::$notice)) {
            $form[] = array(
                "type" => "notice",
                "content" => self::$notice
            );
        }

        $c = array(
            self::$inputs['allow_export_gravity_all']
        );

        if (Config::getAllowExportGravityAll()) {
            $c[] = self::$inputs['allow_export_gravity_subscribe'];
            $c[] = self::$inputs['allow_export_gravity_tag'];
        }

        $form[] = array(
            "type" => "body",
            "title" => "Gravity Form Settings",
            "content" => $c
        );

        if (!Config::getAllowExportGravityAll()) {
            if ( ! class_exists( 'GFFormsModel' ) ) {
                require_once( ABSPATH . 'wp-content/plugins/gravityforms/forms_model.php' );
            }
            if ( ! class_exists( 'GF_Query' ) ) {
                require_once( ABSPATH . 'wp-content/plugins/gravityforms/includes/query/class-gf-query.php' );
            }

            $table = \GFFormsModel::get_forms(1);
            
            foreach ($table as $kk => $vv) {
                $form[] = array(
                    "type" => "body",
                    "title" => "Form ".$vv->title ,// . " (".$vv->entry_count." entries)",
                    "content" => array(
                        array(
                            'tag' => 'input',
                            'type' => 'hidden',
                            'name' => 'allow_export_gravity_data',
                            'extra' => array ( $vv->id, 'id' ),
                            'def' => $vv->id,
                            'placeholder' => null
                        ),
                        array(
                            'label' => 'Tracking',
                            'tag' => 'input',
                            'type' => 'checkbox',
                            'name' => 'allow_export_gravity_data',
                            'extra' => array ( $vv->id, 'status' ),
                            'def' => 0,
                            'placeholder' => null
                        ),
                        array(
                            'label' => 'Contact status',
                            'tag' => 'input',
                            'type' => 'checkbox-sub',
                            'description' => 'What status do you want the contact to have in theMarketer.',
                            'name' => 'allow_export_gravity_data',
                            'extra' => array ( $vv->id, 'subscribe' ),
                            'def' => 0,
                            'placeholder' => null
                        ),
                        array(
                            'label' => 'Tag',
                            'tag' => 'input',
                            'type' => 'text',
                            'name' => 'allow_export_gravity_data',
                            'extra' => array ( $vv->id, 'tag' ),
                            'def' => 0,
                            'placeholder' => null
                        )
                    )
                );
            }
        }

        $form[] = array(
            "type" => "hidden",
            "content" => self::$inputs['onboarding']
        );

        $form[] = array(
            "type" => "footer",
            "content" => array(
                '<div class="mktr-content-left">Need help? <a href="https://themarketer.com/integrations/woocommerce" target="_blank"> Visit our help article <span class="icon mktr-export"></span></a></div>',
                '<div class="mktr-content-right"><button type="submit" class="mktr-button">Save changes <span class="icon mktr-save"></span></button></div><br class="mktr-space"/>'
            )
        );
        echo self::gForm($form);
    }
}
