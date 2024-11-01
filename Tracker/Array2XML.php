<?php
/**
 * @copyright   Copyright (c) 2023 TheMarketer.com
 * @project     TheMarketer.com
 * @website     https://themarketer.com/
 * @author      Alexandru Buzica (EAX LEX S.R.L.) <b.alex@eax.ro>
 * @license     https://opensource.org/licenses/osl-3.0.php - Open Software License (OSL 3.0)
 * @docs        https://themarketer.com/resources/api
 */

/**
 * Usage:
 *  echo Array2XML::cXML("root_node_name",$array)->saveXML();
 */
namespace Mktr\Tracker;

use DOMDocument;
use DOMImplementation;
use Exception;

class Array2XML
{
    const DEFAULT_DOM_VERSION = '1.0';
    const DEFAULT_ENCODING = 'UTF-8';
    const DEFAULT_STANDALONE = false;
    const DEFAULT_FORMAT_OUTPUT = true;

    const LABEL_ATTRIBUTES = '@attributes';
    const LABEL_CDATA = '@cdata';
    const LABEL_DOCTYPE = '@docType';
    const LABEL_VALUE = '@value';

    protected static $xml = null;

    protected static $domVersion = self::DEFAULT_DOM_VERSION;

    protected static $encoding = self::DEFAULT_ENCODING;

    protected static $standalone = self::DEFAULT_STANDALONE;

    protected static $formatOutput = self::DEFAULT_FORMAT_OUTPUT;

    protected static $labelAttributes = self::LABEL_ATTRIBUTES;

    protected static $labelCData = self::LABEL_CDATA;

    protected static $labelDocType = self::LABEL_DOCTYPE;

    protected static $labelValue = self::LABEL_VALUE;

    public static function init(
        string $version = null,
        string $encoding = null,
        bool $standalone = null,
        bool $format_output = null,
        string $labelAttributes = null,
        string $labelCData = null,
        string $labelDocType = null,
        string $labelValue = null
    ) {
        self::setDomVersion($version);
        self::setEncoding($encoding);
        self::setStandalone($standalone);
        self::setFormatOutput($format_output);

        self::setLabelAttributes($labelAttributes);
        self::setLabelCData($labelCData);
        self::setLabelDocType($labelDocType);
        self::setLabelValue($labelValue);

        self::$xml = new DomDocument(self::getDomVersion(), self::getEncoding());
        // self::$xml->xmlStandalone = self::isStandalone();
        self::$xml->formatOutput = self::isFormatOutput();
    }

    public static function getDomVersion(): string
    {
        return self::$domVersion;
    }

    public static function getEncoding(): string
    {
        return self::$encoding;
    }

    /** @noinspection PhpUnused */
    public static function isStandalone(): bool
    {
        return self::$standalone;
    }

    public static function isFormatOutput(): bool
    {
        return self::$formatOutput;
    }

    protected static function setDomVersion(string $domVersion = null)
    {
        self::$domVersion = $domVersion ?? self::DEFAULT_DOM_VERSION;
    }

    protected static function setEncoding(string $encoding = null)
    {
        self::$encoding = $encoding ?? self::DEFAULT_ENCODING;
    }

    protected static function setStandalone(bool $standalone = null)
    {
        self::$standalone = $standalone ?? self::DEFAULT_STANDALONE;
    }

    protected static function setFormatOutput(bool $formatOutput = null)
    {
        self::$formatOutput = $formatOutput ?? self::DEFAULT_FORMAT_OUTPUT;
    }

    /** @noinspection PhpUnused */
    public static function getLabelAttributes(): string
    {
        return self::$labelAttributes;
    }

    /** @noinspection PhpUnused */
    public static function getLabelCData(): string
    {
        return self::$labelCData;
    }

    /** @noinspection PhpUnused */
    public static function getLabelDocType(): string
    {
        return self::$labelDocType;
    }

    /** @noinspection PhpUnused */
    public static function getLabelValue(): string
    {
        return self::$labelValue;
    }

    protected static function setLabelAttributes(string $labelAttributes = null)
    {
        self::$labelAttributes = $labelAttributes ?? self::LABEL_ATTRIBUTES;
    }

    protected static function setLabelCData(string $labelCData = null)
    {
        self::$labelCData = $labelCData ?? self::LABEL_CDATA;
    }

    protected static function setLabelDocType(string $labelDocType = null)
    {
        self::$labelDocType = $labelDocType ?? self::LABEL_DOCTYPE;
    }

    protected static function setLabelValue(string $labelValue = null)
    {
        self::$labelValue = $labelValue ?? self::LABEL_VALUE;
    }

    /** @noinspection PhpUnused */
    public function createXML($node_name, $arr = null, $docType = [])
    {
        return self::cXML($node_name, $arr, $docType);
    }

    public static function cXML($node_name, $arr = null, $docType = [])
    {
        /** @noinspection DuplicatedCode */
        $xml = self::getXMLRoot();
        if ($docType) {
            /** @noinspection PhpExpressionAlwaysNullInspection */
            /** @noinspection PhpUnhandledExceptionInspection */
            $xml->appendChild(
                (new DOMImplementation())
                    ->createDocumentType(
                        $docType['name'] ?? '',
                        $docType['publicId'] ?? '',
                        $docType['systemId'] ?? ''
                    )
            );
        }
        if ($arr == null) {
            foreach ($node_name as $key => $value)
            {
                /** @noinspection PhpExpressionAlwaysNullInspection */
                /** @noinspection PhpUnhandledExceptionInspection */
                $xml->appendChild(self::convert($key, $value));
            }
        } else {
            /** @noinspection PhpExpressionAlwaysNullInspection */
            /** @noinspection PhpUnhandledExceptionInspection */
            $xml->appendChild(self::convert($node_name, $arr));
        }

        self::$xml = null;

        /** @noinspection PhpExpressionAlwaysNullInspection */
        return $xml;
    }

    private static function bool2str($v)
    {
        return $v === true ? 'true' : ($v === false ? 'false' : $v);
    }

    /** @noinspection PhpUnused */
    public function getConvert($node_name, $arr = [])
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return self::convert($node_name, $arr);
    }

    private static function convert($node_name, $arr = [])
    {
        //print_arr($node_name);
        $xml = self::getXMLRoot();
        /** @noinspection PhpExpressionAlwaysNullInspection */
        $node = $xml->createElement($node_name);
        if (is_array($arr)) {
            if (array_key_exists(self::$labelAttributes, $arr) && is_array($arr[self::$labelAttributes])) {
                foreach ($arr[self::$labelAttributes] as $key => $value) {
                    if (!self::isValidTagName($key)) {
                        /** @noinspection PhpUnhandledExceptionInspection */
                        throw new Exception('[Array2XML] Illegal character in attribute name. attribute: '.$key.' in node: '.$node_name);
                    }
                    $node->setAttribute($key, self::bool2str($value));
                }
                unset($arr[self::$labelAttributes]);
            }

            if (array_key_exists(self::$labelValue, $arr)) {
                /** @noinspection PhpExpressionAlwaysNullInspection */
                $node->appendChild($xml->createTextNode(self::bool2str($arr[self::$labelValue])));
                unset($arr[self::$labelValue]);
                return $node;
            } elseif (array_key_exists(self::$labelCData, $arr)) {
                /** @noinspection PhpExpressionAlwaysNullInspection */
                $node->appendChild($xml->createCDATASection(self::bool2str($arr[self::$labelCData])));
                unset($arr[self::$labelCData]);
                return $node;
            }
        }

        if (is_array($arr)) {
            foreach ($arr as $key => $value) {
                if (!self::isValidTagName($key)) {
                    /** @noinspection PhpUnhandledExceptionInspection */
                    throw new Exception('[Array2XML] Illegal character in tag name. tag: '.$key.' in node: '.$node_name);
                }
                if (is_array($value) && is_numeric(key($value))) {
                    /** @noinspection PhpUnusedLocalVariableInspection */
                    foreach ($value as $k => $v) {
                        $node->appendChild(self::convert($key, $v));
                    }
                } else {
                    $node->appendChild(self::convert($key, $value));
                }
                unset($arr[$key]);
            }
        }

        /*
        if (!is_array($arr)) {
            // || preg_match('/[\'^£$%&*()}{@#~? ><>,|=_+¬-]/', $arr)
            if (strlen($arr) > 1000000000) {
                $node->appendChild($xml->createCDATASection(self::bool2str($arr)));
            } else {
                $node->appendChild($xml->createTextNode(self::bool2str($arr)));
            }
        }
        */
        if (!is_array($arr) && !is_null($arr)) {
            /** @noinspection PhpExpressionAlwaysNullInspection */
            $node->appendChild($xml->createTextNode(self::bool2str($arr)));
        }


        return $node;
    }

    private static function getXMLRoot()
    {
        if (empty(self::$xml)) {
            self::init();
        }
        return self::$xml;
    }

    private static function isValidTagName($tag): bool
    {
        /** @noinspection RegExpRedundantEscape */
        /** @noinspection RegExpSimplifiable */
        $pattern = '/^[a-z_]+[a-z0-9\:\-\.\_]*[^:]*$/i';

        return preg_match($pattern, $tag, $matches) && $matches[0] == $tag;
    }
}
