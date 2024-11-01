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

use Mktr\Tracker\Config;
use WP_Error;

class Brands
{
    private static $init = null;

    private static $map = array(
        "fileName" => "brands",
        "secondName" => "brand"
    );

    private static $cat = array();

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
        $brandAttribute = Config::getBrandAttribute();
        $get = array();
        foreach ($brandAttribute as $item) {
            $args = array(
                'taxonomy' => $item,
                'order' => 'DESC'
            );

            $cat = get_terms($args);

            if ($cat instanceof WP_Error)
            {
                $args['taxonomy'] = 'pa_'.$args['taxonomy'];
                $cat = get_terms($args);
            }

            foreach ($cat as $k=>$val)
            {
                if (is_array($val)) {
                    if (!empty($val['name'])) {
                        $get[] = array(
                            "name" => $val['name'],
                            'id'=> $val['term_id'],
                            "url" => get_term_link($val['term_id'])
                            // "image_url" => ''
                        );
                    }
                } else if ($val->name !== null) {
                    $get[] = array(
                        "name" => $val->name,
                        'id'=> $val->term_id,
                        "url" => get_term_link($val->term_id)
                        // "image_url" => ''
                    );
                }
            }
        }

        return $get;
    }
}