<?php
class register_php
{
    public static $name     = 'PHP';
    public static $relocate = true;
};

if (!class_exists('relocate_XAMPP')) {
    class relocate_XAMPP {}
}
class relocate_php extends relocate_XAMPP
{
    private static $relocfiles = array(
        'normal' => array(
            'php\php.ini',
            'php\*.',
            'php\PEAR\pearcmd.php',
            'php\PEAR\peclcmd.php',
            'php\PEAR\PEAR\Info.php'
        ),
        'recur'  => array(
            'php\*.bat'
        )
    );

    private static $relocregs = array(
        'normal' => array(
            'php\pear.ini',
            'php\PEAR\.registry\*.reg'
        )
    );

    public static function Run()
    {
        echo 'relocate '.register_php::$name.PHP_EOL;
        fflush(STDOUT);

        $filelist = array();
        $filelist = array_merge($filelist, self::findFiles(self::$relocfiles['normal']));
        $filelist = array_merge($filelist, self::findFiles(self::$relocfiles['recur'], true));

        foreach ($filelist as $filename) {
            $oldfileperm = fileperms($filename);
            if (!chmod($filename, 0666) && !is_writable($filename)) {
                throw new XAMPPException("File '{$filename}' is not writable.");
            }

            $filecontent = file_get_contents($filename);
            self::relocateString($filecontent);

            file_put_contents($filename, $filecontent);
            chmod($filename, $oldfileperm);
        }


        $filelist = array();
        $filelist = array_merge($filelist, self::findFiles(self::$relocregs['normal']));

        foreach ($filelist as $filename) {
            $oldfileperm = fileperms($filename);
            if (!chmod($filename, 0666) && !is_writable($filename)) {
                throw new XAMPPException("File '{$filename}' is not writable.");
            }

            if ('pear.ini' == basename($filename)) {
                $filecontent   = file($filename);
                $firstline = array_shift($filecontent);
                $filecontent   = implode("\n", $filecontent);
            } else {
                $filecontent = file_get_contents($filename);
            }

            $filecontent = unserialize($filecontent);
            self::relocateArray($filecontent);

            $filecontent = serialize($filecontent);
            if ('pear.ini' == basename($filename)) {
                $filecontent = $firstline.$filecontent;
            }

            file_put_contents($filename, $filecontent);
            chmod($filename, $oldfileperm);
        }

        return;
    }
}
?>
