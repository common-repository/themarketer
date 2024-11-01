<?php
/** @noinspection PhpFullyQualifiedNameUsageInspection */
/**
 * @copyright   Copyright (c) 2023 TheMarketer.com
 * @project     TheMarketer.com
 * @website     https://themarketer.com/
 * @author      Alexandru Buzica (EAX LEX S.R.L.) <b.alex@eax.ro>
 * @license     https://opensource.org/licenses/osl-3.0.php - Open Software License (OSL 3.0)
 * @docs        https://themarketer.com/resources/api
 */

namespace Mktr\Tracker;

class Valid
{
    private static $init = null;
    private static $params = null;
    private static $error = null;

    const mime = array(
        'xml' => 'application/xhtml+xml',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'csv' => 'text/csv'
    );
    private static $getOut = null;

    public static function init()
    {
        if (self::$init == null) {
            self::$init = new self();
        }
        return self::$init;
    }

    public static function getParam($name = null, $def = null)
    {
        if (isset(self::$params[$name])) {
            
            return self::$params[$name];
        } else if(isset($_GET[$name])) {
            self::$params[$name] = sanitize_text_field($_GET[$name]);
            return self::$params[$name];
        }

        self::$params[$name] = $def;
        return $def;
    }

    /** @noinspection PhpUnused */
    public static function setParam($name, $value)
    {
        self::$params[$name] = $value;

        return self::init();
    }

    /** @noinspection PhpUnused */
    public static function validateTelephone($phone)
    {
        return preg_replace("/\D/", "", $phone);
    }


    public static function validateDate($date, $format = 'Y-m-d')
    {
        Config::$dateFormat = $format;
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    /** @noinspection PhpUnused */
    public static function correctDate($date = null, $format = "Y-m-d H:i")
    {
        return $date !== null ? date($format, strtotime($date)) : $date;
    }

    /**
     * @noinspection PhpUnused
     * @noinspection PhpRedundantOptionalArgumentInspection
     */
    public static function digit2($number)
    {
        $number = str_replace(',', '.', $number);
        $number = preg_replace('/\.(?=.*\.)/', '', $number);
        return number_format((float) $number, 2, '.', '');
    }

    public static function check($checkParam = null)
    {
        if ($checkParam === null) {
            return null;
        }

        self::$error = null;

        foreach ($checkParam as $k=>$v) {
            if ($v !== null) {
                $check = explode("|", $v);
                foreach ($check as $do) {
                    if (self::$error === null) {
                        switch ($do) {
                            case "Required":
                                if (self::getParam($k) === null ) {
                                    self::$error = "Missing Parameter ". $k;
                                }
                                break;
                            case "DateCheck":
                                if (self::getParam($k) !== null && !self::validateDate(self::getParam($k))) {
                                    self::$error = "Incorrect Date ".
                                        $k." - ".
                                        self::getParam($k) . " - ".
                                        Config::$dateFormat;
                                }
                                break;
                            case "StartDate":
                                if (self::getParam($k) !== null && strtotime(self::getParam($k)) > \time()) {
                                    self::$error = "Incorrect Start Date ".
                                        $k." - ".
                                        self::getParam($k) . " - Today is ".
                                        date(Config::$dateFormat, \time());
                                }
                                break;
                            case "Key":
                                if (self::getParam($k) !== null && self::getParam($k) !== Config::getRestKey()) {
                                    self::$error = "Incorrect REST API Key ". self::getParam($k);
                                }
                                break;
                            case "RuleCheck":
                                if (self::getParam($k) !== null && Config::getDiscountRules(self::getParam($k)) === null) {
                                    self::$error = "Incorrect Rule Type ". self::getParam($k);
                                }
                                break;
                            case "Int":
                                if (self::getParam($k) !== null && !is_numeric(self::getParam($k))) {
                                    self::$error = "Incorrect Value ". self::getParam($k);
                                }
                                break;
                            case "allow_export":
                                if (Config::getAllowExport() === 0) {
                                    self::$error = "Export not Allow";
                                }
                                break;
                            default:
                        }
                    }
                }
            }
        }

        return self::init();
    }

    public static function status()
    {
        return self::$error == null;
    }

    public static function error()
    {
        return self::$error;
    }

    public static function Output($data, $data1 = null, $name = null, $cron = false)
    {
        $mi = self::getParam('mime-type', Config::defMime);

        if (!headers_sent() || $cron === false) {
            header("Content-type: ".self::mime[$mi]."; charset=utf-8");
            header("HTTP/1.1 200 OK");
            http_response_code(201);
            header("Status: 200 All rosy");
        }

        self::$getOut = "";

        switch ($mi) {
            case "xml":
                if (!is_array($data) && $data1 == null) {
                    self::$getOut = $data;
                } else {
                    if ($data1 == null) {
                        foreach ($data as $key=>$val) {
                            $data = $key;
                            $data1 = $val;
                        }
                    }

                    self::$getOut = Array2XML::cXML($data, $data1)->saveXML();
                }
                break;
            case 'json':
                if ($data1 !== null) {
                    $data = array($data => $data1);
                }
                self::$getOut = self::toJson($data);
                break;
            default:
                self::$getOut = $data;
        }

        return self::$getOut;
    }

    public static function getOutPut()
    {
        return self::$getOut;
    }

    public static function toJson($data = null)
    {
        return json_encode(($data === null ? array() : $data), JSON_UNESCAPED_SLASHES);
    }
}
