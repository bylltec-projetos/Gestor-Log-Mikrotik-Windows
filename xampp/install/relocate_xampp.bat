:: relocate XAMPP
::
:: author     Carsten Wiedmann <carsten_sttgt@gmx.de>
:: copyright  2009 Carsten Wiedmann
:: license    http://www.freebsd.org/copyright/freebsd-license.html FreeBSD License
:: version    2.1
@ECHO OFF & SETLOCAL
PUSHD %~dp0
CD ..

php\php.exe -n -d output_buffering=1 -f "%~f0" -- %1 %2 %3
SET "_return=%ERRORLEVEL%"

POPD
EXIT /B %_return%
<?php
while(@ob_end_clean());

error_reporting(E_ALL | E_STRICT);

require dirname(__FILE__).DIRECTORY_SEPARATOR.'xampp_xampp.php';

class relocate_XAMPP extends XAMPP
{
    protected static $oldlocation  = 'C:\xampp';
    protected static $newlocation  = 'C:\xampp';
    protected static $fromregex    = array();
    protected static $toregex      = array();

    protected static function relocateArray(array &$input)
    {
        $output = array();

        foreach ($input as $key => $value) {
            if (is_array($input[$key])) {
                $output[$key] = $input[$key];
                self::relocateArray($output[$key]);
            } else {
                $changed = array();

                if (is_string($value)) {
                    $changed['value'] = preg_replace(self::$fromregex, self::$toregex, $value);
                }
                if (is_string($key)) {
                    $changed['key'] = preg_replace(self::$fromregex, self::$toregex, $key);
                }

                if (isset($changed['value']) && ($value != $changed['value'])) {
                    $output[$key] = $changed['value'];
                } else {
                    $output[$key] = $value;
                }

                if (isset($changed['key']) && ($key != $changed['key'])) {
                    $output[$changed['key']] = $output[$key];
                    unset($output[$key]);
                }
            }
        }

        $input = $output;

        return;
    }

    protected static function relocateString(&$string)
    {
        $string = preg_replace(self::$fromregex, self::$toregex, $string);

        return;
    }

    protected static function Init()
    {
        parent::Init();

        $GLOBALS['argv'][1] = strtolower($GLOBALS['argv'][1]);

        if (!empty($GLOBALS['argv'][2]) && ('-' != $GLOBALS['argv'][2])) {
            self::$oldlocation = $GLOBALS['argv'][2];
        } else {
            if (is_readable(self::$locationfile)) {
                $oldlocation = trim(file_get_contents(self::$locationfile));
            }
            if (!empty($oldlocation)) {
                self::$oldlocation = $oldlocation;
            }
        }
        if (!empty($GLOBALS['argv'][3])) {
            self::$newlocation = $GLOBALS['argv'][3];
        } else {
            self::$newlocation = dirname(dirname(__FILE__));
        }

        if (false !== $oldlocation = strstr(self::$oldlocation, ':')) {
            $oldlocation = ltrim(self::$oldlocation, ':');
        } else {
            $oldlocation = self::$oldlocation;
        }

        self::$fromregex = array(
            '|(?:[A-Z]:)?(?<!\\\\)'.preg_quote($oldlocation, '|').'(.*)(?!>'.preg_quote($oldlocation, '|').')|iS',
            '|(?:[A-Z]:)?(?<!\\\\)'.preg_quote(str_replace('\\', '\\\\', $oldlocation), '|').'(.*)(?!>'.preg_quote(str_replace('\\', '\\\\', $oldlocation), '|').')|iS',
            '|(?:[A-Z]:)?(?<!/)'.preg_quote(str_replace('\\', '/', $oldlocation), '|').'(.*)(?!>'.preg_quote(str_replace('\\', '/', $oldlocation), '|').')|iS'
        );
        self::$toregex   = array(
            self::$newlocation.'\\1',
            str_replace('\\', '\\\\\\', self::$newlocation).'\\1',
            str_replace('\\', '/', self::$newlocation).'\\1'
        );

        return;
    }

    public static function Run()
    {
        if (empty($GLOBALS['argv'][1])) {
            throw new XAMPPException('No package name given.');
        }

        self::Init();

        $packages = self::getPackages('relocate');

        if ('all' != $GLOBALS['argv'][1]) {
            if (!array_keys($packages, $GLOBALS['argv'][1])) {
                throw new XAMPPException('Package does not exists.');
            }
            $packages = array($GLOBALS['argv'][1]);
        }

        foreach ($packages as $package) {
            $packageclass = 'relocate_'.$package;

            if (!class_exists($packageclass)) {
                throw new XAMPPException("Wrong registration for package '{$package}'.");
            }

            call_user_func(array($packageclass, 'Run'));
        }

        if ('all' == $GLOBALS['argv'][1]) {
            if (file_exists(self::$locationfile)) {
                $oldfileperm = fileperms(self::$locationfile);
                if (!chmod(self::$locationfile, 0666) && !is_writable(self::$locationfile)) {
                    throw new XAMPPException('File \''.self::$locationfile.'\' is not writable.');
                }
            } else {
                $oldfileperm = 0666;
            }

            file_put_contents(self::$locationfile, self::$newlocation.PHP_EOL);
            chmod(self::$locationfile, $oldfileperm);
        }

        return;
    }
}

try {
    relocate_XAMPP::Run();
} catch (XAMPPException $e) {
    fwrite(STDERR, '  ERROR: '.$e->getMessage().PHP_EOL);
    exit(1);
} catch (Exception $e) {
    fwrite(STDERR, $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine().PHP_EOL.'Stack trace:'.PHP_EOL.$e->getTraceAsString().PHP_EOL);
    exit(1);
}

exit(0);
?>
