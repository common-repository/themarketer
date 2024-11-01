<?php
/**
 * Plugin Name:             theMarketer - Email marketing, Newsletters, Automation & Loyalty for Woocommerce
 * Plugin URI:              https://themarketer.com/integrations/woocommerce
 * Description:             Automate and transform the way you communicate with WooCommerce customers and cultivate lasting loyalty using your store’s real-time data.
 * Version:                 1.3.9
 * Requires at least:       4.6
 * Requires PHP:            5.6
 * Author:                  themarketer.com
 * Author URI:              https://themarketer.com
 * Text Domain:             mktr
 * License:                 GPL2
 * License URI:             https://www.gnu.org/licenses/gpl-2.0.html
 * WC requires at least:    4.0.0
 * WC tested up to:         8.4.0
 *
 * @package mktr
 */

defined('ABSPATH') OR exit('No direct script access allowed');

if (!defined('MKTR')) {
    define('MKTR', __FILE__);
}

if (!defined('MKTR_DIR')) {
    define('MKTR_DIR', dirname(__FILE__));
}

if (!defined('MKTR_BASE')) {
    define('MKTR_BASE', plugin_basename(MKTR));
}

if (!defined('MKTR_DIR_NAME')) {
    define('MKTR_DIR_NAME', basename(dirname(MKTR)));
}

if (!defined('MKTR_DEBUG')) {
    define('MKTR_DEBUG', false);
}

if (!defined('MKTR_INSTALL')) {
    define('MKTR_INSTALL', true);
}

if (!defined('MKTR_LEMS')) {
    define('MKTR_LEMS', false);
}

if (!defined('MKTR_VERSION')) {
    define('MKTR_VERSION', 'v1.3.9');
}

require_once MKTR_DIR . '/vendor/autoload.php';

function eDebug() {
    if (isset($_COOKIE['EAX_DEBUG'])) {
        var_dump(func_get_args());
        die();
    }
}

/** @noinspection PhpFullyQualifiedNameUsageInspection */
\Mktr\Tracker\Run::init();
