:: setup XAMPP
::
:: author     Carsten Wiedmann <carsten_sttgt@gmx.de>
:: author     Kay Vogelgesang <kvo@apachefriends.org>
:: copyright  2009 Carsten Wiedmann
:: license    http://www.freebsd.org/copyright/freebsd-license.html FreeBSD License
:: version    2.7
@ECHO OFF & SETLOCAL
PUSHD %~dp0

IF "%1" EQU "extract" (
    POPD
    EXIT /B 0
)

php\php.exe -n -d output_buffering=1 -f "%~f0" -- %1
SET "_return=%ERRORLEVEL%"

POPD

IF %_return% NEQ 0 (
    ECHO.
    IF "%1" NEQ "auto" (
        PAUSE
    )
)
EXIT /B %_return%
<?php
while(@ob_end_clean());

error_reporting(E_ALL | E_STRICT);

require dirname(__FILE__).DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'xampp_xampp.php';

class setup_XAMPP extends XAMPP
{
    private static $updates    = array();
    private static $firstrun   = array();
    private static $locationok = false;
    private static $autoinstall = false;

    protected static function printTitle()
    {
        echo str_repeat(PHP_EOL, 25);
        printf('  %\'#-76s'.PHP_EOL, '');
        printf('  # %-72s #'.PHP_EOL, self::$xamppmodell.' '.self::$xamppversion.' - Setup');
        printf('  #%\'--74s#'.PHP_EOL, '');
        printf('  # %-72s #'.PHP_EOL, 'Copyright 2009 Carsten Wiedmann (FreeBSD License)');
        printf('  #%\'--74s#'.PHP_EOL, '');
        printf('  # %-72s #'.PHP_EOL, 'Authors: Carsten Wiedmann <carsten_sttgt@gmx.de>');
        printf('  # %-72s #'.PHP_EOL, '         Kay Vogelgesang <kvo@apachefriends.org>');
        printf('  %\'#-76s'.PHP_EOL, '');

        return;
    }

    protected static function relocateXAMPP($switch)
    {
        while (1) {
            self::printTitle();

            echo PHP_EOL;
            echo '  Should I make a portable XAMPP without drive letters?'.PHP_EOL;
            echo PHP_EOL;
            echo '  NOTE: - You should use drive letters, if you want use services.'.PHP_EOL;
            echo '        - With USB sticks you must not use drive letters.'.PHP_EOL;
            echo PHP_EOL;
            echo '                      n'.chr(13).'  Your choice? (y/n): ';

            if (self::$autoinstall) {
                echo PHP_EOL;
                $line = 'n';
                break;
            }
            $line = strtolower(trim(fgets(STDIN)));
            if (('' == $line) || ('y' == $line) || ('n' == $line)) {
                break;
            }

            continue;
        }

        $xampppath = self::$xampppath;
        if ('y' == $line) {
            $xampppath = preg_replace('|^(?:[A-Z]:)?(.*)|i', '\\1', $xampppath);
        }

        echo PHP_EOL;
        echo '  relocating XAMPP...'.PHP_EOL;

        $descriptorspec = array(
           0 => array('pipe', 'r'),
           1 => array('pipe', 'w'),
           2 => array('pipe', 'w')
        );
        $pipes = array();

        $process = proc_open('CALL "'.self::$installerpath.DIRECTORY_SEPARATOR."relocate_xampp.bat\" {$switch} - \"{$xampppath}\" 2>&1", $descriptorspec, $pipes);
        if (is_resource($process)) {
            while (false !== ($out = fgets($pipes[1], 80))) {
                echo '  '.trim($out).PHP_EOL;
            }
            fclose($pipes[0]);
            fclose($pipes[1]);
            fclose($pipes[2]);

            $result = proc_close($process);
            if (0 != $result) {
                throw new XAMPPException('relocating XAMPP not successful.');
            }
        } else {
            throw new XAMPPException('Can\'t start process \'relocate_xampp.bat\'.');
        }

        echo '  relocating XAMPP successful.'.PHP_EOL;
        echo PHP_EOL;
        echo '  XAMPP is ready to use.'.PHP_EOL;
        echo PHP_EOL;
        if (!self::$autoinstall) {
            echo '  Press <Return> to continue: ';
            fgets(STDIN);
        }

        return;
    }

    private static function buildMenu(array &$menu)
    {
        $packages = self::getPackages('setup');
        foreach ($packages as $package) {
            if (!method_exists('setup_'.$package, 'Init')) {
                throw new XAMPPException("Wrong registration for package '{$package}'.");
            }
            $packagefunction = call_user_func(array('setup_'.$package, 'Init'));
            foreach ($packagefunction as $key => $value) {
                $menu[] = array(
                    'method' => array('setup_'.$package, $key),
                    'name'   => $value['name'],
                    'value'  => $value['value']
                );
            }
        }

        return;
    }

    private static function doRelocate()
    {
        while(1) {
            self::printTitle();

            echo PHP_EOL;
            if (!self::$locationok) {
                echo '  Current directory does not match configured directory.'.PHP_EOL;
                echo '  I must relocate the XAMPP paths correctly.'.PHP_EOL;
            } else {
                echo '  Should I locate the XAMPP paths correctly?'.PHP_EOL;
            }
            echo PHP_EOL;
            echo '                                      y'.chr(13).'  Should I proceed? (y/x=exit setup): ';

            if (self::$autoinstall) {
                echo PHP_EOL;
                $line = 'y';
                break;
            }

            $line = strtolower(trim(fgets(STDIN)));
            if ('' == $line) {
                $line = 'y';
            }
            if (('x' != $line) && ('y' != $line)) {
                continue;
            }
            if ('x' == $line) {
                echo PHP_EOL;
                throw new XAMPPException('You must locate XAMPP, before you can use it.');
            }

            break;
        }
        echo PHP_EOL;

        self::relocateXAMPP('all');

        return;
    }

    private static function doTimezone()
    {
        restore_error_handler();
        $timezone = @date_default_timezone_get();
        set_error_handler('exception_error_handler');
        $my_ini = self::findFiles(array('mysql\bin\my.ini'));
        $php_ini = self::findFiles(array('php\php.ini'));

        if (!empty($my_ini[0])) {
            $oldfileperm = fileperms($my_ini[0]);
            if (!chmod($my_ini[0], 0666) && !is_writable($my_ini[0])) {
                throw new XAMPPException("File '{$my_ini[0]}' is not writable.");
            }
            $filecontent = file_get_contents($my_ini[0]);
            $filecontent = preg_replace('|([#]*)?default-time-zone\\h*=.*|i', 'default-time-zone       = "'.$timezone.'"', $filecontent);
            file_put_contents($my_ini[0], $filecontent);
            chmod($my_ini[0], $oldfileperm);
        }

        if (!empty($php_ini[0])) {
            $oldfileperm = fileperms($php_ini[0]);
            if (!chmod($php_ini[0], 0666) && !is_writable($php_ini[0])) {
                throw new XAMPPException("File '{$my_ini[0]}' is not writable.");
            }
            $filecontent = file_get_contents($php_ini[0]);
            $filecontent = preg_replace('|([;]*)?date\\.timezone\\h*=.*|i', 'date.timezone = "'.$timezone.'"', $filecontent);
            file_put_contents($php_ini[0], $filecontent);
            chmod($php_ini[0], $oldfileperm);
        }

        self::printTitle();

        echo PHP_EOL;
        echo "  I have set the timezone in 'php.ini' and 'my.ini' to \"{$timezone}\".".PHP_EOL;
        echo PHP_EOL;
        echo '  You should correct these values if my guess was wrong.'.PHP_EOL;
        echo PHP_EOL;

        if (!self::$autoinstall) {
            echo '  Press <Return> to continue: ';
            fgets(STDIN);
        }

        return;
    }

    private static function doShortcuts()
    {
        while(1) {
            self::printTitle();

            echo PHP_EOL;
            echo '                                                          y'.chr(13).'  Should I add shortcuts to the startmenu/desktop? (y/n): ';

            if (self::$autoinstall) {
                echo PHP_EOL;
                $line = 'y';
                break;
            }

            $line = strtolower(trim(fgets(STDIN)));
            if ('' == $line) {
                $line = 'y';
            }
            if (('n' != $line) && ('y' != $line)) {
                continue;
            }
            if ('n' == $line) {
                return;
            }

            break;
        }

        $WshShell   = new COM('WScript.Shell', null, CP_UTF8);
        $FSO        = new COM('Scripting.FileSystemObject', null, CP_UTF8);

        $desktop    = $WshShell->SpecialFolders('Desktop');
        $startmenu  = $WshShell->SpecialFolders('Programs');
        $startmenu  = $FSO->BuildPath($startmenu, utf8_encode('XAMPP for Windows'));
        $xampppath  = utf8_encode(self::$xampppath);

        $links = array();
        $links[$FSO->BuildPath($desktop, utf8_encode('XAMPP Control Panel.lnk'))] = array(
            'TargetPath'       => $FSO->BuildPath($xampppath, utf8_encode('xampp-control.exe')),
            'WorkingDirectory' => $xampppath,
            'WindowStyle'      => 1,
            'IconLocation'     => $FSO->BuildPath($xampppath, utf8_encode('xampp-control.exe')),
            'Description'      => utf8_encode('XAMPP Control Panel')
        );
        $links[$FSO->BuildPath($startmenu, utf8_encode('XAMPP Control Panel.lnk'))] = array(
            'TargetPath'       => $FSO->BuildPath($xampppath, utf8_encode('xampp-control.exe')),
            'WorkingDirectory' => $xampppath,
            'WindowStyle'      => 1,
            'IconLocation'     => $FSO->BuildPath($xampppath, utf8_encode('xampp-control.exe')),
            'Description'      => utf8_encode('XAMPP Control Panel')
        );
        $links[$FSO->BuildPath($startmenu, utf8_encode('XAMPP Setup.lnk'))] = array(
            'TargetPath'       => $FSO->BuildPath($xampppath, utf8_encode('xampp_setup.bat')),
            'WorkingDirectory' => $xampppath,
            'WindowStyle'      => 1,
            'IconLocation'     => $FSO->BuildPath($xampppath, utf8_encode('xampp_cli.exe')),
            'Description'      => utf8_encode('XAMPP Setup')
        );
        $links[$FSO->BuildPath($startmenu, utf8_encode('XAMPP Shell.lnk'))] = array(
            'TargetPath'       => $FSO->BuildPath($xampppath, utf8_encode('xampp_shell.bat')),
            'WorkingDirectory' => $xampppath,
            'WindowStyle'      => 1,
            'IconLocation'     => $FSO->BuildPath($xampppath, utf8_encode('xampp_cli.exe')),
            'Description'      => utf8_encode('XAMPP Shell')
        );
        $links[$FSO->BuildPath($startmenu, utf8_encode('XAMPP Uninstall.lnk'))] = array(
            'TargetPath'       => $FSO->BuildPath($xampppath, utf8_encode('uninstall_xampp.bat')),
            'WorkingDirectory' => $xampppath,
            'WindowStyle'      => 1,
            'IconLocation'     => $FSO->BuildPath($xampppath, utf8_encode('xampp_cli.exe')),
            'Description'      => utf8_encode('XAMPP Uninstall')
        );

        if (!$FSO->FolderExists($desktop)) {
            $FSO->CreateFolder($desktop);
        }
        if (!$FSO->FolderExists($startmenu)) {
            $FSO->CreateFolder($startmenu);
        }

        foreach ($links as $shortcut => $value) {
            try {
                if (!$FSO->FileExists($shortcut)) {
                    $FSO->CreateTextFile($shortcut);
                }
                $shortcut_file = $FSO->GetFile($shortcut);
                $oldfileperm = $shortcut_file->attributes;

                if (($oldfileperm & 1) == 1) {
                    $shortcut_file->attributes += -1;
                }
            } catch (Exception $e) {
                throw new XAMPPException('File \''.utf8_decode($shortcut).'\' is not writable.');
            }

            $ShellLink                   = $WshShell->CreateShortcut($shortcut);
            $ShellLink->TargetPath       = $value['TargetPath'];
            $ShellLink->WorkingDirectory = $value['WorkingDirectory'];
            $ShellLink->WindowStyle      = $value['WindowStyle'];
            $ShellLink->IconLocation     = $value['IconLocation'];
            $ShellLink->Description      = $value['Description'];
            $ShellLink->Save();
            $ShellLink = null;

            $shortcut_file->attributes = $oldfileperm;
            $shortcut_file = null;
        }

        $FSO      = null;
        $WshShell = null;
        return;
    }

    private static function doUpdates()
    {
        while(1) {
            self::printTitle();

            echo PHP_EOL;
            echo '  I have found some updates.'.PHP_EOL;
            echo PHP_EOL;
            echo '  Should I install them now?'.PHP_EOL;
            echo '  (Please make sure that all XAMPP components are stopped!)'.PHP_EOL;
            echo PHP_EOL;
            echo '                                 y'.chr(13).'  Your choice? (y/x=exit setup): ';

            if (self::$autoinstall) {
                echo PHP_EOL;
                $line = 'y';
                break;
            }

            $line = strtolower(trim(fgets(STDIN)));
            if ('' == $line) {
                $line = 'y';
            }
            if (('x' != $line) && ('y' != $line)) {
                continue;
            }
            if ('x' == $line) {
                echo PHP_EOL;
                throw new XAMPPException('You should install these updates, before you use XAMPP the nest time.');
            }

            break;
        }

        echo PHP_EOL;

        foreach (self::$updates as $package) {
            include $package;

            $classname = preg_replace('|.*\\\\update_(.*)\.php$|ie', "strtolower('\\1')", $package);
            if (!method_exists('update_'.$classname, 'update'.ucfirst($classname))) {
                throw new XAMPPException('Wrong update file for package "'.$classname.'".');
            }

            call_user_func(array('update_'.$classname, 'update'.ucfirst($classname)));

            unlink($package);
        }

        echo '  All updates OK.'.PHP_EOL;
        echo PHP_EOL;
        echo '  XAMPP is ready to use.'.PHP_EOL;
        echo PHP_EOL;
        if (!self::$autoinstall) {
            echo '  Press <Return> to continue: ';
            fgets(STDIN);
        }

        return;

    }

    protected static function Init()
    {
        parent::Init();

        if (!empty($GLOBALS['argv'][1]) && ('auto' == $GLOBALS['argv'][1])) {
            self::$autoinstall = true;
        } else {
            self::$autoinstall = false;
        }

        self::$updates    = self::findFiles(array('install\update_*.php'));
        self::$firstrun   = self::findFiles(array('install\xampp_firstrun.txt'));
        self::$locationok = self::checkLocation();

        return;
    }

    public static function Run()
    {
        self::Init();

        if (!empty(self::$updates)) {
            self::doUpdates();
            self::$firstrun = array();
        }

        if (!empty(self::$firstrun)) {
            self::doShortcuts();
        }

        if (!self::$locationok || !empty(self::$firstrun)) {
            self::doRelocate();
        }

        if (!empty(self::$firstrun)) {
            self::doTimezone();

            if (!is_writeable(self::$firstrun[0])) {
                throw new XAMPPException('Can\'t delete file \''.self::$firstrun[0].'\'.');
            }
            unlink(self::$firstrun[0]);
        }

        if (self::$autoinstall) {
            return;
        }

        while(1) {
            self::printTitle();

            $menu = array(null);
            self::buildMenu($menu);
            unset($menu[0]);

            echo PHP_EOL;
            foreach ($menu as $key => $value) {
                printf('  %d. %s'.PHP_EOL, $key, $value['name']);
            }

            echo PHP_EOL;
            echo '  x  Exit'.PHP_EOL;

            echo PHP_EOL;
            echo '  Please choose (1-'.count($menu).'/x): ';

            $line = strtolower(trim(fgets(STDIN)));
            if ('x' == $line) {
                echo PHP_EOL;
                echo '  Exit Setup.';
                return;
            }
            if ((1 > $line) || (count($menu) < $line)) {
                continue;
            }

            call_user_func_array($menu[$line]['method'], array($menu[$line]['value']));
        }

        return;
    }
}

try {
    setup_XAMPP::Run();
} catch (XAMPPException $e) {
    fwrite(STDERR, '  ERROR: '.$e->getMessage().PHP_EOL);
    exit(1);
} catch (Exception $e) {
    fwrite(STDERR, $e->getMessage().' in '.$e->getFile().' on line '.$e->getLine().PHP_EOL.'Stack trace:'.PHP_EOL.$e->getTraceAsString().PHP_EOL);
    exit(1);
}

exit(0);
?>
