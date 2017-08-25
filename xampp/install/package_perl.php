<?php
class register_perl
{
    public static $name     = 'Perl';
    public static $relocate = true;
    public static $setup    = true;
};

if (!class_exists('relocate_XAMPP')) {
    class relocate_XAMPP {}
}
class relocate_perl extends relocate_XAMPP
{
    private static $relocfiles = array(
        'normal' => array(
            'perl\bin\*.',
            'perl\bin\*.pl',
            'perl\bin\*.bat',
            'perl\lib\Config_heavy.pl',
            'perl\lib\perllocal.pod',
            'perl\site\lib\ppm.xml',
            'perl\lib\Config.pm',
            'perl\lib\CPAN\Config.pm',
            'perl\lib\CPANPLUS\Config\System.pm',
            'perl\lib\CORE\config.h'
        ),
        'recur'  => array(
            'perl\.packlist'
        )
    );

    public static function Run()
    {
        echo 'relocate '.register_perl::$name.PHP_EOL;
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

        return;
    }
}

if (!class_exists('setup_XAMPP')) {
    class setup_XAMPP {}
}
class setup_perl extends setup_XAMPP
{
    private static $functions = array(
        'mod_perl' => array(
            'name'  => 'enable/disable mod_perl',
            'value' => null
        ),
        'asp' => array(
            'name'  => 'enable/disable Apache::ASP',
            'value' => null
        )
    );

    private static $httpdconf = 'C:\xampp\apache\conf\httpd.conf';
    private static $perlconf  = 'C:\xampp\apache\conf\extra\perl.conf';
    private static $startup   = 'C:\xampp\apache\conf\extra\startup.pl';

    private static $mod_perl  = null;
    private static $asp       = null;

    public static function Init()
    {
        self::$httpdconf = self::$xampppath.DIRECTORY_SEPARATOR.'apache'.DIRECTORY_SEPARATOR.'conf'.DIRECTORY_SEPARATOR.'httpd.conf';
        self::$perlconf  = self::$xampppath.DIRECTORY_SEPARATOR.'apache'.DIRECTORY_SEPARATOR.'conf'.DIRECTORY_SEPARATOR.'extra'.DIRECTORY_SEPARATOR.'perl.conf';
        self::$startup   = self::$xampppath.DIRECTORY_SEPARATOR.'apache'.DIRECTORY_SEPARATOR.'conf'.DIRECTORY_SEPARATOR.'extra'.DIRECTORY_SEPARATOR.'startup.pl';

        self::mod_perlStatus();
        self::aspStatus();

        return self::$functions;
    }

    private static function mod_perlStatus()
    {
        if (is_readable(self::$httpdconf)) {
            $confcontent = file_get_contents(self::$httpdconf);

            $matches = array();
            $confline = preg_quote('Include "conf/extra/perl.conf"', '|');
            if (!preg_match('|([#]*)?\\h*'.$confline.'|i', $confcontent, $matches)) {
                self::$functions['mod_perl']['name']  = 'enable mod_perl';
                self::$functions['mod_perl']['value'] = 1;
                self::$mod_perl = 0;
                return;
            }
            if (!empty($matches[1])) {
                self::$functions['mod_perl']['name']  = 'enable mod_perl';
                self::$functions['mod_perl']['value'] = 1;
                self::$mod_perl = 0;
                return;
            }

            self::$functions['mod_perl']['name']  = 'disable mod_perl';
            self::$functions['mod_perl']['value'] = 0;
            self::$mod_perl = 1;
            return;
        }

        unset(self::$functions['mod_perl']);
        self::$mod_perl = null;

        return;
    }

    public static function mod_perl($switch)
    {
        $oldfileperm = fileperms(self::$httpdconf);
        if (!chmod(self::$httpdconf, 0666) && !is_writable(self::$httpdconf)) {
            throw new XAMPPException('File \''.self::$httpdconf.'\' is not writable.');
        }
        $confcontent = file_get_contents(self::$httpdconf);
        $confline    = preg_quote('Include "conf/extra/perl.conf"', '|');

        switch ($switch) {
        case 0:
            $confcontent = preg_replace('|\\h*('.$confline.')|i', '#\\1', $confcontent);
            break;
        case 1:
            if (!preg_match('|([#]*)?\\h*'.$confline.'|i', $confcontent)) {
                $confcontent .= PHP_EOL
                              . '# Perl settings'.PHP_EOL
                              . 'Include "conf/extra/perl.conf"'.PHP_EOL;
            } else {
                $confcontent = preg_replace('|[#]*\\h*('.$confline.')|i', '\\1', $confcontent);
            }
            break;
        }

        file_put_contents(self::$httpdconf, $confcontent);
        chmod(self::$httpdconf, $oldfileperm);

        return;
    }

    private static function aspStatus()
    {
        if (0 == self::$mod_perl) {
            self::$functions['asp']['name']  = 'Apache::ASP is disabled, because mod_perl is disabled';
            self::$functions['asp']['value'] = 1;
            self::$asp = 0;
            
            return;
        }
            
        if (is_readable(self::$perlconf)) {
            $confcontent = file_get_contents(self::$perlconf);

            $matches = array();
            $confline = preg_quote('Include "conf/extra/asp.conf"', '|');
            if (!preg_match('|([#]*)?\\h*'.$confline.'|i', $confcontent, $matches)) {
                self::$functions['asp']['name']  = 'enable Apache::ASP';
                self::$functions['asp']['value'] = 1;
                self::$asp = 0;
                return;
            }
            if (!empty($matches[1])) {
                self::$functions['asp']['name']  = 'enable Apache::ASP';
                self::$functions['asp']['value'] = 1;
                self::$asp = 0;
                return;
            }

            self::$functions['asp']['name']  = 'disable Apache::ASP';
            self::$functions['asp']['value'] = 0;
            self::$asp = 1;
            return;
        }

        unset(self::$functions['asp']);
        self::$asp = null;

        return;
    }

    public static function asp($switch)
    {
        if (0 == self::$mod_perl) {
            return;
        }

        $oldfileperm = fileperms(self::$perlconf);
        if (!chmod(self::$perlconf, 0666) && !is_writable(self::$perlconf)) {
            throw new XAMPPException('File \''.self::$perlconf.'\' is not writable.');
        }
        $confcontent = file_get_contents(self::$perlconf);
        $confline    = preg_quote('Include "conf/extra/asp.conf"', '|');

        switch ($switch) {
        case 0:
            $confcontent = preg_replace('|\\h*('.$confline.')|i', '#\\1', $confcontent);
            break;
        case 1:
            if (!preg_match('|([#]*)?\\h*'.$confline.'|i', $confcontent)) {
                $confcontent .= PHP_EOL
                              . '# ASP settings'.PHP_EOL
                              . 'Include "conf/extra/asp.conf"'.PHP_EOL;
            } else {
                $confcontent = preg_replace('|[#]*\\h*('.$confline.')|i', '\\1', $confcontent);
            }
            break;
        }

        file_put_contents(self::$perlconf, $confcontent);
        chmod(self::$perlconf, $oldfileperm);


        $oldfileperm = fileperms(self::$startup);
        if (!chmod(self::$startup, 0666) && !is_writable(self::$startup)) {
            throw new XAMPPException('File \''.self::$startup.'\' is not writable.');
        }
        $confcontent = file_get_contents(self::$startup);
        $confline    = preg_quote('use Apache::ASP ();', '|');

        switch ($switch) {
        case 0:
            $confcontent = preg_replace('|\\h*('.$confline.')|i', '#\\1', $confcontent);
            break;
        case 1:
            if (!preg_match('|([#]*)?\\h*'.$confline.'|i', $confcontent)) {
                $confline    = preg_quote('1;', '|');
                $confcontent = preg_replace('|('.$confline.')|i', 'use Apache::ASP ();'.PHP_EOL.'\\1', $confcontent);
            } else {
                $confcontent = preg_replace('|[#]*\\h*('.$confline.')|i', '\\1', $confcontent);
            }
            break;
        }

        file_put_contents(self::$startup, $confcontent);
        chmod(self::$startup, $oldfileperm);

        return;
    }
}
?>
