<?php
class register__xampp
{
    public static $name     = 'XAMPP base package';
    public static $relocate = true;
    public static $setup    = true;
};

if (!class_exists('relocate_XAMPP')) {
    class relocate_XAMPP {}
}
class relocate__xampp extends relocate_XAMPP
{
    private static $relocfiles = array();

    private static function relocateShortcut()
    {
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

        foreach ($links as $shortcut => $value) {
            if (!$FSO->FileExists($shortcut)) {
                continue;
            }

            try {
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

    public static function Run()
    {
        echo 'relocate '.register__xampp::$name.PHP_EOL;
        fflush(STDOUT);

        self::relocateShortcut();

        return;
    }
}

if (!class_exists('setup_XAMPP')) {
    class setup_XAMPP {}
}
class setup__xampp extends setup_XAMPP
{
    private static $functions = array(
        'control_panel'  => array(
            'name'  => 'start XAMPP Control Panel',
            'value' => ''
        ),
        'relocate'  => array(
            'name'  => 'relocate XAMPP',
            'value' => 'all'
        )
    );

    private static $currlocation = 'C:\xampp';

    public static function Init()
    {
        self::checkLocation(self::$currlocation);
        self::$functions['relocate']['name'] = 'relocate XAMPP'.PHP_EOL.'     (current path: '.self::$currlocation.')';

        return self::$functions;
    }

    public static function control_panel($switch)
    {
        $descriptorspec = array(
           0 => array('pipe', 'r'),
           1 => array('pipe', 'w'),
           2 => array('pipe', 'w')
        );
        $pipes = array();

        $process = proc_open('START "" "'.self::$xampppath.DIRECTORY_SEPARATOR.'xampp-control.exe"', $descriptorspec, $pipes);
        if (is_resource($process)) {
            fclose($pipes[0]);
            fclose($pipes[1]);
            fclose($pipes[2]);

            $result = proc_close($process);
            if (0 != $result) {
                throw new XAMPPException('Can\'t start process \'xampp-control.exe\'.');
            }
        } else {
            throw new XAMPPException('Can\'t start process \'xampp-control.exe\'.');
        }

        return;
    }

    public static function relocate($switch)
    {
        self::relocateXAMPP($switch);

        return;
    }
}
?>
