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

use Mktr\Tracker\Valid;
use Mktr\Tracker\Config;

class clearEvents
{
    private static $init = null;
    private static $map = array();

    public static function get($f = 'fileName')
    {
        if (isset(self::$map[$f])) {
            return self::$map[$f];
        }
        return null;
    }

    public static function init()
    {
        if (self::$init == null) {
            self::$init = new self();
        }
        return self::$init;
    }

    public static function execute()
    {
		
        Valid::setParam('mime-type', 'json');

        $eventData = Config::session()->get("ClearMktr");

        if (!empty($eventData)) {
            
            foreach ($eventData as $key => $value) {
                $eventData1 = Config::session()->get($key);

                foreach ($value as $value1) {
                    unset($eventData1[$value1]);
                }

                Config::session()->set($key, $eventData1);
            }

            Config::session()->set("ClearMktr", array());
        }

		$r = "console.log(2);";
        
        return json_encode([0]);
    }
}
