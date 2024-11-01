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
use Mktr\Tracker\Valid;
use WC_Coupon;

class DiscountCode
{
    private static $init = null;
    private static $ruleType;
    private static $code = null;

    const PREFIX = 'MKTR-';
    const NAME = "MKTR-%s-%s";
    const LENGTH = 10;
    const DESCRIPTION = "Discount Code Generated through TheMarketer API";

    const SYMBOLS_COLLECTION = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

    public static function init()
    {
        if (self::$init == null) {
            self::$init = new self();
        }
        return self::$init;
    }

    public static function newCode()
    {
        self::$code = self::PREFIX;
        for ($i = 0, $indexMax = strlen(self::SYMBOLS_COLLECTION) - 1; $i < self::LENGTH; ++$i) {
            self::$code .= substr(self::SYMBOLS_COLLECTION, random_int(0, $indexMax), 1);
        }

        if (wc_get_coupon_id_by_code(self::$code) !== 0)
        {
            self::newCode();
        }

        return self::$code;
    }

    public static function getNewCode() {
        $coupon = new WC_Coupon();

        $coupon->set_code(self::newCode());

        /* fixed_cart | fixed_product | percent */
        /* Mktr free_shipping */
        $type = Config::getDiscountRules(Valid::getParam('type'));
        $value = Valid::getParam('value');
        $expiration = Valid::getParam('expiration_date');

        if ($type === 'free_shipping')
        {
            $coupon->set_discount_type('percent');
            $coupon->set_free_shipping(true);
        } else {
            $coupon->set_discount_type($type);
        }

        $coupon->set_amount($value);

        if ($expiration !== null)
        {
            // $coupon->set_date_expires(strtotime(Valid::getParam('expiration_date')));
            $coupon->set_date_expires($expiration);

        }
        $coupon->set_description(self::DESCRIPTION." (".$type."-".$value.( $expiration === null ? '' : '-'.$expiration).")");
        // $coupon->set_individual_use()
        $coupon->set_usage_limit(1);

        //save the coupon
        $coupon->save();

        return self::$code;
    }

}