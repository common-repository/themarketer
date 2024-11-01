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
use Mktr\Tracker\Config;
use Mktr\Tracker\Data;
use Mktr\Tracker\Valid;

class Reviews
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
        $t = Valid::getParam('start_date', date('Y-m-d'));
        $o = Api::send("product_reviews", array(
            't' => strtotime($t)
        ), false);

        $xml = simplexml_load_string($o->getContent(), 'SimpleXMLElement', LIBXML_NOCDATA);
        // $added = array();
        // $data = Data::init();
        // $revStore = $data->{"reviewStore".Config::getRestKey()};

        foreach ($xml->review as $value) {
            if (isset($value->review_date)) {
				$c = get_comments(array(
					'author_email' => (string) $value->review_email,
					'search' => (string) $value->review_text,
					'post_id' => (string) $value->product_id
				));
				
				if (empty($c)) {
                    $add = array(
                            'comment_post_ID'      => (string) $value->product_id, // <=== The product ID where the review will show up
                            'comment_author'       => (string) $value->review_author,
                            'comment_author_email' => (string) $value->review_email, // <== Important
                            'comment_author_url'   => '',
                            'comment_content'      => (string) $value->review_text,
                            'comment_type'         => '',
                            'comment_parent'       => 0,
                            // 'user_id'              => 5, // <== Important
                            'comment_author_IP'    => '',
                            'comment_agent'        => '',
                            'comment_date'         => (string) $value->review_date, // date('Y-m-d H:i:s'),
                            'comment_approved'     => 1,
                        );
                    $user = get_user_by('email', (string) $value->review_email);

                    if ($user) {
                        $add['user_id'] = $user->ID;
                    }
                    $comment_id = wp_insert_comment($add);

                    update_comment_meta( $comment_id, 'rating', round(((int)$value->rating / 2)) );

                    // $added[(string) $value->review_id] = $comment_id;
				} else {
                    // $added[(string) $value->review_id] = $c[0]->comment_ID;
					// $add['comment_ID'] = $c[0]->comment_ID;
					// wp_update_comment($add);
                }
            }
        }

        // $data->{"reviewStore".Config::getRestKey()} = $added;

        // $data->save();

        return $xml;
    }
}