<?php
/**
 * @copyright   Copyright (c) 2023 TheMarketer.com
 * @project     TheMarketer.com
 * @website     https://themarketer.com/
 * @author      Alexandru Buzica (EAX LEX S.R.L.) <b.alex@eax.ro>
 * @license     https://opensource.org/licenses/osl-3.0.php - Open Software License (OSL 3.0)
 * @docs        https://themarketer.com/resources/api
 */

namespace Mktr\Tracker\Model;

use Mktr\Tracker\Config;
use Mktr\Tracker\Data;
use Mktr\Tracker\FileSystem;
use Mktr\Tracker\Routes\Feed;
use Mktr\Tracker\Routes\Reviews;
use Mktr\Tracker\Valid;

class Cron
{
    private static $init = null;
    public static function init()
    {
        if (self::$init == null) {
            self::$init = new self();
        }
        return self::$init;
    }

    public static function cronAction() {
        global $sitepress;

        set_time_limit(3600);
        ini_set('memory_limit', '2G');

        //  $sitepress->switch_lang()
        $data = Data::init();
        $upFeed = $data->update_feed;
        $upReview = $data->update_review;
        $data->cron_start = time();
        $data->save();
        
        if (Config::getStatus() != 0) {

            if (Config::getCronFeed() != 0 && $upFeed < time()) {

                if ($sitepress !== null) {
                    $langs = $sitepress->get_active_languages();
                } else {
                    $langs = ['noLang' => 'lang'];
                }
                
                foreach ($langs as $key => $value) {
                    if ($key !== 'noLang') { $sitepress->switch_lang($key); }
                    Valid::setParam('mime-type','xml');
                    $run = Feed::init();
                    
                    Valid::Output($run->get('fileName'), array( $run->get('secondName') => $run->execute()), null, true);
    
                    $fileName = $run->get('fileName') . "." . ( $key !== 'noLang' ? $key . '.' : '' ) . Valid::getParam('mime-type','xml');
                    
                    FileSystem::writeFile($fileName, Valid::getOutPut());
                }

                $data->update_feed = strtotime("+" . Config::getUpdateFeed() . " hour");
                $data->cron_end_feed = time();
                $data->save();
            }

            if (Config::getCronReview() != 0 && $upReview < time()) {

                Reviews::execute();

                $data->update_review = strtotime("+".Config::getUpdateReview()." hour");
                $data->cron_end_review = time();
                $data->save();
            }
        }

        return $data->getData();
    }
}
