<?php
function exception_error_handler($errno, $errstr, $errfile, $errline ) {
    $errorName = array(
        E_ERROR              => 'ERROR',
        E_WARNING            => 'WARNING',
        E_PARSE              => 'PARSING ERROR',
        E_NOTICE             => 'NOTICE',
        E_CORE_ERROR         => 'CORE ERROR',
        E_CORE_WARNING       => 'CORE WARNING',
        E_COMPILE_ERROR      => 'COMPILE ERROR',
        E_COMPILE_WARNING    => 'COMPILE WARNING',
        E_USER_ERROR         => 'USER ERROR',
        E_USER_WARNING       => 'USER WARNING',
        E_USER_NOTICE        => 'USER NOTICE',
        E_STRICT             => 'STRICT NOTICE',
        E_RECOVERABLE_ERROR  => 'RECOVERABLE ERROR'
    );
    throw new ErrorException("{$errorName[$errno]}: {$errstr}", $errno, 0, $errfile, $errline);
}
set_error_handler('exception_error_handler');

class XAMPPException extends Exception {}

class XAMPP
{
    protected static $xampppath     = 'C:\xampp';
    protected static $installerpath = 'C:\xampp\install';
    protected static $locationfile  = 'C:\xampp\install\xampp_location.txt';
    protected static $versionfile   = 'C:\xampp\install\xampp_version.txt';
    protected static $modellfile    = 'C:\xampp\install\xampp_modell.txt';
    protected static $xamppversion  = '';
    protected static $xamppmodell   = 'XAMPP';

    private static $packages = array();

    protected static function findFiles(array $files, $recur = false)
    {
        $result = array();
        if (empty($files)) {
            return $result;
        }

        if ($recur) {
            foreach ($files as &$file) {
                $file = '"'.self::$xampppath.DIRECTORY_SEPARATOR.$file.'"';
            }
            unset($file);
            $files = implode(' ', $files);
            exec("DIR /S /B /A:-D /O:N {$files} 2>nul", $result);
        } else {
            foreach ($files as $file) {
                $currfiles = array();
                exec('DIR /B /A:-D /O:N "'.self::$xampppath.DIRECTORY_SEPARATOR.$file.'" 2>nul', $currfiles);
                foreach ($currfiles as $currfile) {
                    $result[] = self::$xampppath.DIRECTORY_SEPARATOR.dirname($file).DIRECTORY_SEPARATOR.$currfile;
                }
            }
        }

        return $result;
    }

    protected static function checkLocation(&$currlocationstring = null)
    {
        if (!is_readable(self::$locationfile)) {
            throw new XAMPPException('Can\'t read file \''.self::$locationfile.'\'.');
        }
        $currlocationstring = trim(file_get_contents(self::$locationfile));
        $currlocation       = trim(file_get_contents(self::$locationfile));
        preg_match('|^([A-Z]:)?(.*)|i', $currlocation, $currlocation);

        $xampppath = self::$xampppath;
        preg_match('|^([A-Z]:)?(.*)|i', $xampppath, $xampppath);

        if ($currlocation[2] != $xampppath[2]) {
            return false;
        }
        if (empty($currlocation[1])) {
            return true;
        }
        if (strtolower($currlocation[1]) != strtolower($xampppath[1])) {
            return false;
        }
        return true;
    }

    final private static function registerPackages()
    {
        $packages = self::findFiles(array('install\package_*.php'));

        foreach ($packages as $package) {
            if (!is_readable($package)) {
                continue;
            }

            include $package;
            $classname = preg_replace('|.*\\\\package_(.*)\.php$|ie', "strtolower('\\1')", $package);

            if (class_exists('register_'.$classname)) {
                self::$packages[$classname] = get_class_vars('register_'.$classname);
            }
        }

        return;
    }

    final protected static function getPackages($type)
    {
        $packages = array();

        foreach (self::$packages as $key => $value) {
            if (!empty($value[$type])) {
                $packages[] = $key;
            }
        }

        return $packages;
    }

    protected static function Init()
    {
        self::$xampppath     = dirname(dirname(__FILE__));
        self::$installerpath = dirname(__FILE__);
        self::$locationfile  = self::$installerpath.DIRECTORY_SEPARATOR.basename(self::$locationfile);
        self::$versionfile   = self::$installerpath.DIRECTORY_SEPARATOR.basename(self::$versionfile);
        self::$modellfile    = self::$installerpath.DIRECTORY_SEPARATOR.basename(self::$modellfile);

        if (is_readable(self::$versionfile)) {
            self::$xamppversion = trim(file_get_contents(self::$versionfile));
        }
        if (is_readable(self::$modellfile)) {
            self::$xamppmodell = trim(file_get_contents(self::$modellfile));
        }

        self::registerPackages();

        return;
    }
}
?>
