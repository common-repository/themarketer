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

class FileSystem
{
    private static $path = null;
    private static $lastPath = null;
    private static $status = array();
    private static $useRoot = false;

    private static $init = null;

    public static function init() {
        if (self::$init == null) {
            self::$init = new self();
        }
        return self::$init;
    }

    /** @noinspection PhpUnused */
    public static function setWorkDirectory($name = 'Storage')
    {
        if ($name != 'base' && !self::$useRoot)
        {
            self::$path = Config::getDir() . $name . "/";
        } else {
            self::$path = ABSPATH;
        }

        return self::init();
    }

    /** @noinspection PhpUnused */
    public static function writeFile($fName, $content, $mode = 'w+')
    {
        self::$lastPath = self::getPath() . $fName;

        $file = fopen(self::$lastPath, $mode);
        fwrite($file, $content);
        fclose($file);

        self::$status[] = [
            'path' => self::getPath(),
            'fileName' => $fName,
            'fullPath' => self::getPath() . $fName,
            'status' => true
        ];

        return self::init();
    }

    /** @noinspection PhpUnused */
    public static function rFile($fName, $mode = "rb")
    {
        self::$lastPath = self::getPath() . $fName;

        if(self::fileExists($fName) && filesize(self::$lastPath) > 0)
        {
            $file = fopen(self::$lastPath, $mode);

            $contents = fread($file, filesize(self::$lastPath));

            fclose($file);

            return $contents;
        } else {
            return '';
        }
    }

    /** @noinspection PhpUnused */
    public static function readFile($fName, $mode = "rb")
    {
        return self::rFile($fName, $mode);
    }

    /** @noinspection PhpUnused */
    public static function fileExists($fName)
    {
        return file_exists(self::getPath() . $fName);
    }

    /** @noinspection PhpUnused */
    public static function deleteFile($fName)
    {
        self::$lastPath = self::getPath() . $fName;

        if(self::fileExists($fName))
        {
            unlink(self::$lastPath);
        }
        return true;
    }

    public static function getPath()
    {
        if (self::$path == null)
        {
            self::setWorkDirectory();
        }
        return self::$path;
    }

    /** @noinspection PhpUnused */
    public static function getLastPath()
    {
        return self::$lastPath;
    }

    public static function getStatus()
    {
        return self::$status;
    }
}
