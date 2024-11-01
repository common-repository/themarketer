<?php
/** @noinspection SpellCheckingInspection */
/**
 * @copyright   Copyright (c) 2023 TheMarketer.com
 * @project     TheMarketer.com
 * @website     https://themarketer.com/
 * @author      Alexandru Buzica (EAX LEX S.R.L.) <b.alex@eax.ro>
 * @license     https://opensource.org/licenses/osl-3.0.php - Open Software License (OSL 3.0)
 * @docs        https://themarketer.com/resources/api
 */

namespace Mktr\Tracker;

/**
 * @method static getStatus()
 * @method static getOnboarding()
 * @method static getKey()
 * @method static getRestKey()
 * @method static getCustomerId()
 * @method static getOptIn()
 * @method static getPushStatus()
 * @method static getDefaultStock()
 * @method static getAllowExport()
 * @method static getBrandAttribute()
 * @method static getColorAttribute()
 * @method static getSizeAttribute()
 * @method static getCronFeed()
 * @method static getUpdateFeed()
 * @method static getCronReview()
 * @method static getUpdateReview()
 * @method static getSelectors()
 */
class Config
{
    public static $name = 'mktr';
    public static $dateFormat = "Y-m-d H:i";
    
    public static $MKTR_TABLE = null;
    public static $MKTR_DB = null;
    public static $product_cat = null;
    public static $cMStatus = null;

    const space = PHP_EOL . "        ";
    /* TODO Google Test */
    const Google = true;
    const defMime = 'xml';

    /*
    const discountRules = [
        0 => "fixedValue",
        1 => "percentage",
        2 => "freeShipping"
    ];
    */

    const discountRules = [
        0 => "fixed_cart",
        1 => "percent",
        2 => "free_shipping"
    ];

    const configNames = array(
        'redirect' => 'mktr_tracker/tracker/redirect',
        'status' => 'mktr_tracker/tracker/status',
        'onboarding' => 'mktr_tracker/tracker/onboarding',
        'js_file' => 'mktr_tracker/tracker/js_file',
        'tracking_key' => 'mktr_tracker/tracker/tracking_key',
        'rest_key' => 'mktr_tracker/tracker/rest_key',
        'customer_id'=>'mktr_tracker/tracker/customer_id',
        'cron_feed' => 'mktr_tracker/tracker/cron_feed',
        'update_feed' => 'mktr_tracker/tracker/update_feed',
        'cron_review' => 'mktr_tracker/tracker/cron_review',
        'update_review' => 'mktr_tracker/tracker/update_feed',
        'opt_in' => 'mktr_tracker/tracker/opt_in',
        'opt_in_oldmail' => 'mktr_tracker/tracker/opt_in_oldmail',
        'mailpoet_id_list' => 'mktr_tracker/tracker/mailpoet_id_list',
        'push_status' => 'mktr_tracker/tracker/push_status',
        'default_stock' => 'mktr_tracker/tracker/default_stock',
        'allow_export' => 'mktr_tracker/tracker/allow_export',
        'allow_export_gravity' => 'mktr_tracker/tracker/allow_export_gravity',
        'allow_export_gravity_all' => 'mktr_tracker/tracker/allow_export_gravity_all',
        'allow_export_gravity_data' => 'mktr_tracker/tracker/allow_export_gravity_data',
        'allow_export_gravity_subscribe' => 'mktr_tracker/tracker/allow_export_gravity_subscribe',
        'allow_export_gravity_tag' => 'mktr_tracker/tracker/allow_export_gravity_tag',
        'add_description' => 'mktr_tracker/tracker/add_description',
        'selectors' => 'mktr_tracker/tracker/selectors',
        'brand' => 'mktr_tracker/attribute/brand',
        'color' => 'mktr_tracker/attribute/color',
        'size' => 'mktr_tracker/attribute/size',
        'google_status' => 'mktr_google/google/status',
        'google_tagCode' => 'mktr_google/google/tagCode',
        'woocommerce_version' => 'woocommerce_version',
        'rated' => 'mktr_tracker/tracker/rated',
        'rated_install' => 'mktr_tracker/tracker/rated_install',
        'translate_press' => 'mktr_tracker/tracker/translate_press'
    );

    const configDefaults = array(
        'redirect' => 0,
        'status' => 1,
        'onboarding' => 2,
        'js_file' => null,
        'tracking_key' => '',
        'rest_key' => '',
        'customer_id'=>'',
        'cron_feed' => 1,
        'update_feed' => 4,
        'cron_review' => 0,
        'update_review' => 4,
        'opt_in' => 0,
        'opt_in_oldmail' => null,
        'mailpoet_id_list' => null,
        'push_status' => 0,
        'default_stock' => 0,
        'allow_export' => 0,
        'allow_export_gravity' => 0,
        'allow_export_gravity_all' => 1,
        'allow_export_gravity_data' => null,
        'allow_export_gravity_subscribe' => 0,
        'allow_export_gravity_tag' => '',
        'add_description' => 0,
        'selectors' => '.single_add_to_cart_button,.remove_from_cart_button,.mailpoet_submit,.wc-block-cart-item__remove-link,.add_to_cart_button,.woocommerce-cart-form .product-remove > a,a.remove,.wd-wishlist-btn',
        'brand' => 'brand',
        'color' => 'color',
        'size' => 'size',
        'google_status' => 1,
        'google_tagCode' => '',
        'woocommerce_version' => null,
        'rated' => 0,
        'rated_install' => 0,
        'translate_press' => 0,
    );

    const funcNames = array(
        'getStatus' => array('status', 'int'),
        'getOnboarding' => array('onboarding', 'int'),
        'getJsFile' => array('js_file', false),
        'getKey' => array('tracking_key', false),
        'getRestKey' => array('rest_key', false),
        'getCustomerId' => array('customer_id', false),
        'getOptIn' => array('opt_in', 'int'),
        'getPushStatus' => array('push_status', 'int'),
        'getSelectors' => array('selectors', false),
        'getDefaultStock' => array('default_stock', 'int'),
        'getAllowExport' => array('allow_export', 'int'),
        'getAllowExportGravity' => array('allow_export_gravity', 'int'),
        'getAllowExportGravityAll' => array('allow_export_gravity_all', 'int'),
        'getAllowExportGravitySubscribe' => array('allow_export_gravity_subscribe', 'int'),
        'getAllowExportGravityTag' => array('allow_export_gravity_tag', false),
        'getAddDescription' => array('add_description', 'int'),
        'getBrandAttribute' => array('brand', false),
        'getColorAttribute' => array('color', false),
        'getSizeAttribute' => array('size', false),
        'getCronFeed' => array('cron_feed', 'int'),
        'getUpdateFeed' => array('update_feed', 'int'),
        'getCronReview' => array('cron_review', 'int'),
        'getUpdateReview' => array('update_review', 'int'),
        'getRated' => array('rated', 'int'),
        'getRatedInstall' => array('rated_install', 'int'),
        'getTranslatePress' => array('translate_press', 'int')
    );

    public static $checkList = ['key', 'start_date', 'end_date', 'page', 'customerId','expiration_date', 'value','type', 'mime-type', 'read','file'];

    const FireBase = 'const firebaseConfig = {
    apiKey: "AIzaSyA3c9lHIzPIvUciUjp1U2sxoTuaahnXuHw",
    projectId: "themarketer-e5579",
    messagingSenderId: "125832801949",
    appId: "1:125832801949:web:0b14cfa2fd7ace8064ae74"
};

firebase.initializeApp(firebaseConfig);';
/* TODO: LINK */ 
    const FireBaseMessaging = 'importScripts("https://www.gstatic.com/firebasejs/9.4.0/firebase-app-compat.js");
importScripts("https://www.gstatic.com/firebasejs/9.4.0/firebase-messaging-compat.js");
importScripts("./firebase-config.js");
importScripts("https://t.themarketer.com/firebase.js");';

    private static $configValues = array();

    public static $MKTR = null;
    public static $MKTR_DIR = null;
    public static $MKTR_PLUGIN = null;
    public static $MKTR_PLUGIN_BASENAME = null;
    public static $MKTR_SVG = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz48c3ZnIGlkPSJiIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB2aWV3Qm94PSIwIDAgMTA5LjU5IDk1Ljk2Ij48ZGVmcz48bGluZWFyR3JhZGllbnQgaWQ9ImQiIHgxPSI5Ni40IiB5MT0iMzUuMTUiIHgyPSI5Ni4wMyIgeTI9Ijk2LjExIiBncmFkaWVudFRyYW5zZm9ybT0ibWF0cml4KDEsIDAsIDAsIDEsIDAsIDApIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHN0b3Agb2Zmc2V0PSIwIiBzdG9wLWNvbG9yPSIjMWY2Y2ZmIi8+PHN0b3Agb2Zmc2V0PSIxIiBzdG9wLWNvbG9yPSIjMDA0NmNlIi8+PC9saW5lYXJHcmFkaWVudD48bGluZWFyR3JhZGllbnQgaWQ9ImUiIHgxPSItMi44OCIgeTE9IjQ4LjM3IiB4Mj0iMTcuNzgiIHkyPSIzMy4wNSIgeGxpbms6aHJlZj0iI2QiLz48bGluZWFyR3JhZGllbnQgaWQ9ImYiIHgxPSIyLjUyIiB5MT0iNjkuODYiIHgyPSIzMy4xNSIgeTI9Ijk2LjIiIGdyYWRpZW50VHJhbnNmb3JtPSJ0cmFuc2xhdGUoMjYuNTQgMTY1LjM5KSByb3RhdGUoMTgwKSIgeGxpbms6aHJlZj0iI2QiLz48bGluZWFyR3JhZGllbnQgaWQ9ImciIHgxPSI3LjE4IiB5MT0iNC42NCIgeDI9IjYyLjQxIiB5Mj0iNzkuNjMiIHhsaW5rOmhyZWY9IiNkIi8+PGxpbmVhckdyYWRpZW50IGlkPSJoIiB4MT0iMzMuMTkiIHkxPSI3MS43NiIgeDI9IjEwMS45MSIgeTI9IjcuMjQiIHhsaW5rOmhyZWY9IiNkIi8+PC9kZWZzPjxnIGlkPSJjIj48Zz48cGF0aCBkPSJNMTA5LjU5LDEzLjI3VjgyLjdjMCwzLjY2LTEuNDgsNi45OC0zLjg5LDkuMzgtMi40LDIuNC01LjcyLDMuODktOS4zOCwzLjg5LTcuMzMsMC0xMy4yNy01Ljk0LTEzLjI3LTEzLjI3VjEzLjI3YzAtNy4zMyw1Ljk0LTEzLjI3LDEzLjI3LTEzLjI3czEzLjI3LDUuOTQsMTMuMjcsMTMuMjdaIiBzdHlsZT0iZmlsbDp1cmwoI2QpOyIvPjxwYXRoIGQ9Ik0yNi41NCwxMy4yN3Y1Mi45M0gxMy4yN2MtNy4zMywwLTEzLjI3LTUuOTQtMTMuMjctMTMuMjdWMTMuMjdjMC0uMTIsMC0uMjUsMC0uMzcsMC0uMTMsMC0uMjUsLjAyLS4zOCwwLS4xMiwuMDEtLjI0LC4wMi0uMzZ2LS4wNGMuMTMtMS41MywuNTItMi45OCwxLjEzLTQuMzEsLjAxLS4wMiwuMDItLjA1LC4wMy0uMDgsLjA1LS4xMSwuMS0uMjEsLjE1LS4zMiwuMDUtLjEsLjEtLjIxLC4xNi0uMzFsLjAzLS4wN2MuMDQtLjA4LC4wOS0uMTcsLjEzLS4yNSwuMDMtLjA3LC4wNy0uMTMsLjExLS4xOSwuMDktLjE2LC4xOS0uMzMsLjI5LS40OCwuMDYtLjA5LC4xMi0uMTgsLjE4LS4yNywuMTItLjE4LC4yNC0uMzUsLjM3LS41MiwuMjYtLjM0LC41My0uNjcsLjgxLS45OCwuMTQtLjE2LC4yOS0uMzEsLjQ0LS40NiwuMTMtLjEzLC4yNy0uMjYsLjQtLjM4LC4wMi0uMDIsLjA1LS4wNSwuMDctLjA3LC4wNy0uMDcsLjE1LS4xMywuMjItLjIsLjA2LS4wNSwuMTItLjEsLjE4LS4xNSwuMDgtLjA3LC4xNi0uMTMsLjI1LS4yLC4yOC0uMjIsLjU3LS40MywuODctLjYzbC4wNC0uMDNjLjE4LS4xMiwuMzctLjI0LC41NS0uMzUsMCwwLC4wMSwwLC4wMi0uMDEsLjE3LS4xLC4zMy0uMTksLjUtLjI4LC4wNS0uMDMsLjExLS4wNiwuMTYtLjA4LC4xNy0uMDksLjM1LS4xOCwuNTItLjI2LC4wNy0uMDMsLjE0LS4wNywuMjEtLjEsLjAyLDAsLjA0LS4wMiwuMDYtLjAzLC4yMi0uMSwuNDQtLjE5LC42Ni0uMjdsLjA5LS4wM2MuMTQtLjA1LC4yOC0uMSwuNDItLjE1LC4wMywwLC4wNi0uMDIsLjA5LS4wMywuMDYtLjAyLC4xMy0uMDQsLjE5LS4wNmguMDFjLjA4LS4wMywuMTYtLjA1LC4yNC0uMDgsLjE3LS4wNSwuMzQtLjA5LC41MS0uMTMsLjA2LS4wMiwuMTMtLjAzLC4xOS0uMDQsLjA4LS4wMiwuMTYtLjAzLC4yNC0uMDUsLjExLS4wMiwuMjItLjA0LC4zMy0uMDYsLjE5LS4wMywuMzgtLjA2LC41Ny0uMDksLjA3LDAsLjE0LS4wMiwuMjEtLjAzLC4wNywwLC4xNC0uMDIsLjIxLS4wMiwuMSwwLC4yLS4wMiwuMy0uMDMsMCwwLC4wMSwwLC4wMiwwLC4xLDAsLjItLjAxLC4yOS0uMDIsLjAzLDAsLjA2LDAsLjA5LDAsLjA4LDAsLjE1LDAsLjIzLDAsLjEzLDAsLjI3LDAsLjQsMCwuMTIsMCwuMjUsMCwuMzcsMCwuMTMsMCwuMjUsMCwuMzgsLjAyLC4xMiwwLC4yNCwuMDEsLjM1LC4wMmguMDZjLjEyLC4wMSwuMjMsLjAzLC4zNSwuMDQsMS40LC4xNiwyLjczLC41MywzLjk2LDEuMDksLjAzLC4wMSwuMDUsLjAyLC4wOCwuMDQsLjExLC4wNSwuMjEsLjEsLjMxLC4xNSwuMDksLjA0LC4xOCwuMDksLjI4LC4xNCwuMDUsLjAyLC4wOSwuMDUsLjE0LC4wNywuMDUsLjAyLC4wOSwuMDUsLjE0LC4wOCwuMDYsLjAzLC4xMiwuMDcsLjE4LC4xLC4wMSwwLC4wMiwuMDEsLjAzLC4wMiwuMjcsLjE2LC41NCwuMzIsLjc5LC40OSwuNTMsLjM2LDEuMDMsLjc1LDEuNSwxLjE4LC4zMSwuMjksLjYxLC41OSwuOSwuOSwuMDcsLjA4LC4xNCwuMTYsLjIxLC4yNCwuMDYsLjA2LC4xMSwuMTMsLjE2LC4xOSwuMDUsLjA2LC4xLC4xMiwuMTQsLjE4LC4wMiwuMDMsLjA0LC4wNSwuMDcsLjA4LC4wOCwuMSwuMTYsLjIsLjIzLC4zbC4wNSwuMDZjMS40OSwyLjAyLDIuNDMsNC40OCwyLjU3LDcuMTUsLjAxLC4yNCwuMDIsLjQ4LC4wMiwuNzJaIiBzdHlsZT0iZmlsbDp1cmwoI2UpOyIvPjxwYXRoIGQ9Ik0xMy4yNyw2OS40M2gwYzcuMzIsMCwxMy4yNyw1Ljk1LDEzLjI3LDEzLjI3aDBjMCw3LjMyLTUuOTUsMTMuMjctMTMuMjcsMTMuMjdIMHYtMTMuMjdDMCw3NS4zNyw1Ljk1LDY5LjQzLDEzLjI3LDY5LjQzWiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMjYuNTQgMTY1LjM5KSByb3RhdGUoLTE4MCkiIHN0eWxlPSJmaWxsOnVybCgjZik7Ii8+PHBhdGggZD0iTTY3LjkyLDcwLjc2czAsLjAzLDAsLjA1Yy0uNDgsMy4zNy0yLjI2LDYuNTUtNS4yLDguNzQtMi4zOCwxLjc4LTUuMTYsMi42My03LjkyLDIuNjMtNC4wNCwwLTguMDQtMS44NC0xMC42NC01LjM0bC0xNy42MS0yMy42MS00Ljc0LTYuMzVoLS4wMUwyLjYzLDIxLjJjLTEuMzgtMS44NS0yLjItMy45My0yLjUtNi4wNi0uMDktLjYyLS4xMy0xLjI0LS4xMy0xLjg2SDBjMC0uMTMsMC0uMjYsMC0uMzgsMC0uMTMsMC0uMjUsLjAyLS4zOCwwLS4xMiwuMDEtLjI0LC4wMi0uMzZ2LS4wNGMuMTMtMS40OCwuNTEtMi45NCwxLjEzLTQuMzEsLjAxLS4wMiwuMDItLjA1LC4wMy0uMDgsLjA1LS4xMSwuMS0uMjEsLjE1LS4zMiwuMDUtLjEsLjEtLjIxLC4xNi0uMzFsLjAzLS4wN2MuMDQtLjA4LC4wOS0uMTcsLjEzLS4yNSwuMDMtLjA3LC4wNy0uMTMsLjExLS4xOSwuMDktLjE2LC4xOS0uMzIsLjI5LS40OCwuMDYtLjA5LC4xMi0uMTgsLjE4LS4yNywuMTItLjE4LC4yNC0uMzUsLjM3LS41MiwuMjUtLjM0LC41Mi0uNjcsLjgxLS45OCwuMTQtLjE2LC4yOS0uMzEsLjQ0LS40NiwuMTMtLjEzLC4yNi0uMjYsLjQtLjM4LC4wMi0uMDIsLjA1LS4wNSwuMDctLjA3LC4wNy0uMDcsLjE1LS4xMywuMjItLjIsLjA2LS4wNSwuMTItLjEsLjE4LS4xNSwuMDgtLjA3LC4xNi0uMTMsLjI1LS4yLC4xMS0uMDgsLjIxLS4xNywuMzItLjI1LC4zNy0uMjgsLjc1LS41MywxLjE0LS43NiwwLDAsLjAxLDAsLjAyLS4wMSwuMzktLjIzLC43OC0uNDQsMS4xOC0uNjIsLjA3LS4wMywuMTQtLjA3LC4yMS0uMSwuMDIsMCwuMDQtLjAyLC4wNi0uMDMsLjIyLS4xLC40NC0uMTksLjY2LS4yN2wuMDktLjAzYy4xNC0uMDUsLjI4LS4xLC40Mi0uMTUsLjAzLDAsLjA2LS4wMiwuMDktLjAzLC4wNi0uMDIsLjEzLS4wNCwuMTktLjA2aC4wMWMuMDgtLjAzLC4xNi0uMDUsLjI0LS4wOCwuMTctLjA1LC4zNC0uMDksLjUxLS4xMywuMDYtLjAyLC4xMy0uMDMsLjE5LS4wNCwuMDgtLjAyLC4xNi0uMDMsLjI0LS4wNSwuMTEtLjAyLC4yMi0uMDQsLjMzLS4wNiwuMTktLjAzLC4zOC0uMDYsLjU3LS4wOSwuMDcsMCwuMTQtLjAyLC4yMS0uMDMsLjA3LDAsLjE0LS4wMiwuMjEtLjAyLC4xLDAsLjItLjAyLC4zLS4wMywwLDAsLjAxLDAsLjAyLDAsLjEsMCwuMi0uMDEsLjI5LS4wMiwuMDMsMCwuMDYsMCwuMDksMCwuMDgsMCwuMTUsMCwuMjMsMCwuMTMsMCwuMjcsMCwuNCwwLC4xMiwwLC4yNSwwLC4zNywwLC4xMywwLC4yNSwwLC4zOCwuMDIsLjEyLDAsLjI0LC4wMSwuMzUsLjAyaC4wNmMuMTIsLjAxLC4yMywuMDMsLjM1LC4wNCwxLjM2LC4xNiwyLjcsLjUyLDMuOTYsMS4wOSwuMDMsLjAxLC4wNSwuMDIsLjA4LC4wNCwuMTEsLjA1LC4yMSwuMSwuMzEsLjE1LC4wOSwuMDQsLjE4LC4wOSwuMjgsLjE0LC4wNSwuMDIsLjA5LC4wNSwuMTQsLjA3LC4wNSwuMDIsLjA5LC4wNSwuMTQsLjA4LC4wNiwuMDMsLjEyLC4wNywuMTgsLjEsLjAxLDAsLjAyLC4wMSwuMDMsLjAyLC4yNywuMTUsLjUzLC4zMiwuNzksLjQ5LC41MywuMzUsMS4wMywuNzQsMS41LDEuMTgsLjMxLC4yOCwuNjEsLjU4LC45LC45LC4wNywuMDgsLjE0LC4xNiwuMjEsLjI0LC4wNiwuMDYsLjExLC4xMywuMTYsLjE5LC4wNSwuMDYsLjEsLjEyLC4xNCwuMTgsLjAyLC4wMywuMDQsLjA1LC4wNywuMDgsLjA4LC4xLC4xNiwuMiwuMjMsLjNsLjA1LC4wNiwyNy44OCwzNy4zOCwxMy41OSwxOC4yMWMyLjE4LDIuOTIsMi45Nyw2LjQ0LDIuNSw5Ljc4WiIgc3R5bGU9ImZpbGw6dXJsKCNnKTsiLz48cGF0aCBkPSJNMTA5LjQ2LDE1LjEyYy0uMywyLjEzLTEuMTMsNC4yMy0yLjUxLDYuMDhsLTQxLjU1LDU1LjcxYy01LjMyLDcuMTQtMTYuMDIsNy4xMS0yMS4zMS0uMDVsLTUuNzktNy44NS05Ljg1LTEzLjItNi43Mi05YzguOTksMTAuNjMsMjUuNTgsMTAuMDgsMzMuODktMS4wOUw4NS42OCw1LjM0YzQuMzgtNS44NywxMi42OS03LjA4LDE4LjU3LTIuNyw0LjAyLDMsNS44Niw3Ljg1LDUuMjEsMTIuNDlaIiBzdHlsZT0iZmlsbDp1cmwoI2gpOyIvPjwvZz48L2c+PC9zdmc+';

    private static $init = null;

    public function __get($name)
    {
        return self::getValue($name);
    }

    public function __set($name, $value)
    {
        self::setValue($name, $value);
    }

    public static function __callStatic($name, $arguments)
    {
        return self::callNow($name);
    }

    public function __call($name, $arguments)
    {
        return self::callNow($name);
    }

    public static function session() {
        return Session::init();
        /*
        if ( ! WC()->session ) {
			WC()->initialize_session();
		}
        return WC()->session;
        */
    }
    
    public static function getProductCat() {
        if (Config::$product_cat === null) {
            Config::$product_cat = apply_filters('marketer_override_product_category', 'product_cat');
        }
        return Config::$product_cat;
    }
    
    public static function getMailPoetId( $id = null ) {
        $i = Config::getValue('mailpoet_id_list');
        if ($id === false || Config::getValue('mailpoet_id_list') === null) {
            $repo = \MailPoet\DI\ContainerWrapper::getInstance()->get(\MailPoet\Segments\SegmentsRepository::class);
            
            $i = array();
            $TheMarketerID = null;
            $listType = array(\MailPoet\Entities\SegmentEntity::TYPE_WC_USERS, \MailPoet\Entities\SegmentEntity::TYPE_WP_USERS);

            foreach($repo->findAll() as $v) {
                if ($v->getName() === 'TheMarketer') {
                    $TheMarketerID = $v->getId();
                    $i[] = (string) $v->getId();
                } else if (in_array($v->getType(), $listType)) {
                    $i[] = (string) $v->getId();
                }
            }
            
            try {
                $sAdd = $repo->createOrUpdate('TheMarketer', 'TheMarketer List', \MailPoet\Entities\SegmentEntity::TYPE_DEFAULT, [], $TheMarketerID, true);
            } catch ( \Exception $e ) {
                return null;
            }

            Config::setValue('mailpoet_id_list', $i);
        }
        return $i;
    }

    public static function getSubscriber($customerEmail) {

        if (class_exists(\MailPoet\API\API::class)) {
            try {
                $mailpoet_api = \MailPoet\API\API::MP('v1');
                return $mailpoet_api->getSubscriber($customerEmail);
            } catch (\Exception $e){
                $data = false;
            }

            return $data;
        }

        if (\MailPoet\Settings\SettingsController::getInstance()->get('woocommerce.optin_on_checkout.enabled') == 1) {
            try {
                $ids = self::getMailPoetId();
                if (is_array ($ids)) {
                    $data = \MailPoet\Models\Subscriber::tableAlias('subscribers')
                    ->select('subscribers.*')
                    ->where('subscribers.email', $customerEmail)
                    ->join( MP_SUBSCRIBER_SEGMENT_TABLE, 'relation.subscriber_id = subscribers.id', 'relation' )
                    ->whereIn('relation.segment_id', $ids)
                    ->findOne();
                } else {
                    $data = \MailPoet\Models\Subscriber::tableAlias('subscribers')
                    ->select('subscribers.*')
                    ->where('subscribers.email', $customerEmail)
                    ->join( MP_SUBSCRIBER_SEGMENT_TABLE, 'relation.subscriber_id = subscribers.id', 'relation' )
                    ->where('relation.segment_id', $ids)
                    ->findOne();
                }
            } catch (\Exception $e){
                $data = false;
            }

            return $data;
        }

        return \MailPoet\Models\Subscriber::findOne($customerEmail);
    }

    public static function mStatus() {
        if (self::$cMStatus == null) {
            if (class_exists(\MailPoet\Entities\SubscriberEntity::class)) {
                self::$cMStatus = \MailPoet\Entities\SubscriberEntity::STATUS_SUBSCRIBED;
            } else {
                self::$cMStatus = \MailPoet\Models\Subscriber::STATUS_SUBSCRIBED;
            }
        }
        return self::$cMStatus;
    }

    public static function tableName()
    {
        if (self::$MKTR_TABLE == null) { self::$MKTR_TABLE = self::db()->prefix . 'mktr_session'; }
        return self::$MKTR_TABLE;
    }

    public static function db()
    {
        if (self::$MKTR_DB == null) { global $wpdb; self::$MKTR_DB = $wpdb; }
        return self::$MKTR_DB;
    }

    public static function GET($key, $default = false) {
        return in_array($key, self::$checkList) && array_key_exists($key, $_GET) ? sanitize_text_field($_GET[$key]) : $default;
    }

    public static function POST($key) {
        if (Config::$name === $key && isset($_POST[$key])) {
            return self::sanitize($key, $_POST);
        }
        return null;
    }

    public static function sanitize($key, $data, $f = false) {
        if (is_array($data[$key])) {
            $list = [];
            foreach ($data[$key] as $k => $v) {
                if ($f === true) {
                    $list[$k] = self::sanitize($k, $data[$key], true);
                } else {
                    $list[$key][$k] = self::sanitize($k, $data[$key], true);
                }
            }
            return $list;
        } else {
            return sanitize_text_field($data[$key]);
        }
    }

    public static function REQUEST($key) {
        if (isset($_REQUEST[$key])) {
            if (is_array($_REQUEST[$key])) {
                $list = [];
                foreach ($_REQUEST[$key] as $k=>$v) {
                    $list[$key][$k] = sanitize_text_field($v);
                }
                return $list[$key];
            } else {
                return sanitize_text_field($_REQUEST[$key]);
            }
        }
        return null;
    }
    
    private static function callNow($name)
    {
        $func = self::funcNames;

        if (isset($func[$name]))
        {
            switch ($func[$name][1]) {
                case 'int':
                    return (int) self::getValue($func[$name][0]);
                default:
                    return self::getValue($func[$name][0]);
            }
        }
        return null;
    }

    public static function init() {
        if (self::$init == null) {
            self::$init = new self();
        }

        return self::$init;
    }

    public static function getBaseURL()
    {
        if (Config::getTranslatePress() == 1) {
            $site_url = get_site_url();
            $request_uri = $_SERVER['REQUEST_URI'];
            $segments = explode('/', trim($request_uri, '/'));
            $first_segment = isset($segments[0]) ? $segments[0] : '';
            if (self::isValidLanguageCode($first_segment)) {
                return $site_url . '/' . $first_segment . '/';
            }
        
            return $site_url . '/';
        }

        return get_site_url(). '/';
    }
    
    public static function isValidLanguageCode($code)
    {
        $supported_languages = array('en-ch', 'fr-ch', 'de-ch', 'it-ch'); // Adjust this list to your site’s languages
        
        return in_array($code, $supported_languages);
    }

    /** @noinspection PhpUnused */
    public static function getDiscountRules($get = null)
    {
        if (is_null($get)) {
            return self::discountRules;
        }

        $check = self::discountRules;

        if (isset($check[$get]))
        {
            return self::discountRules[$get];
        }
        return null;
    }

    public static function getValue($name = null)
    {
        if (array_key_exists($name, self::$configValues)) {
            return self::$configValues[$name];
        }

        if (array_key_exists($name, Config::configNames))
        {
            self::$configValues[$name] = get_option(Config::configNames[$name], null);

            if (self::$configValues[$name] === null)
            {
                update_option(Config::configNames[$name], Config::configDefaults[$name]);
                self::$configValues[$name] = get_option(Config::configNames[$name], null);
            }

            if (in_array($name, array('color','size','brand')))
            {
                self::$configValues[$name] = explode("|", self::$configValues[$name]);
            }

            return self::$configValues[$name];
        }

        return null;
    }

    public static function setValue($name, $value)
    {
        if (array_key_exists($name, Config::configNames))
        {
            update_option(Config::configNames[$name], $value);
            self::$configValues[$name] = get_option(Config::configNames[$name], null);
        } else {
            self::$configValues[$name] = $value;
        }
        return self::init();
    }

    /** @noinspection PhpUnused */
    public static function getSVG()
    {
        return self::$MKTR_SVG;
    }

    /** @noinspection PhpUnused */
    public static function getLoader()
    {
        if (self::$MKTR == null)
        {
            self::$MKTR = MKTR;
        }

        return self::$MKTR;
    }
    /** @noinspection PhpUnused */
    public static function getDir()
    {
        if (self::$MKTR_DIR == null)
        {
            self::$MKTR_DIR = MKTR_DIR . '/';
        }

        return self::$MKTR_DIR;
    }

    /** @noinspection PhpUnused */
    public static function getPlugin()
    {
        if (self::$MKTR_PLUGIN == null)
        {
            self::$MKTR_PLUGIN = dirname(plugin_basename(MKTR));
        }

        return self::$MKTR_PLUGIN;
    }

    /** @noinspection PhpUnused */
    public static function getPluginBase()
    {
        if (self::$MKTR_PLUGIN_BASENAME == null)
        {
            self::$MKTR_PLUGIN_BASENAME = plugin_basename(MKTR);
        }

        return self::$MKTR_PLUGIN_BASENAME;
    }

    /** @noinspection PhpUnused */
    public static function getFireBase()
    {
        return self::FireBase;
    }

    /** @noinspection PhpUnused */
    public static function getFireBaseMessaging()
    {
        return self::FireBaseMessaging;
    }

    /** @noinspection PhpUnused */
    public static function cleanSetup()
    {
        foreach (Config::configNames as $value)
        {
            delete_option($value);
        }

        return self::init();
    }
}