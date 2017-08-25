<?php
class register_apache
{
    public static $name     = 'Apache';
    public static $relocate = true;
    public static $setup    = true;
};

if (!class_exists('relocate_XAMPP')) {
    class relocate_XAMPP {}
}
class relocate_apache extends relocate_XAMPP
{
    private static $relocfiles = array(
        'normal' => array(
            'apache\build\config_vars.mk',
            'apache\bin\apr-1-config.*',
            'apache\bin\apu-1-config.*',
            'apache\bin\apxs.*'
        ),
        'recur'  => array(
            'apache\conf\*.conf',
            'cgi-bin\*.pl',
            'cgi-bin\*.cgi',
            'cgi-bin\*.asp'
        )
    );

    public static function Run()
    {
        echo 'relocate '.register_apache::$name.PHP_EOL;
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
class setup_apache extends setup_XAMPP
{
    private static $functions = array(
        'mod_ssl'     => array(
            'name'  => 'enable/disable HTTPS (SSL)',
            'value' => null
        ),
        'mod_include' => array(
            'name'  => 'enable/disable Server Side Includes (SSI)',
            'value' => null
        ),
        'ip'          => array(
            'name'  => 'enable IPv4/6 (auto)',
            'value' => null
        )
    );

    private static $httpdconf   = 'C:\xampp\apache\conf\httpd.conf';
    private static $sslconf     = 'C:\xampp\apache\conf\extra\httpd-ssl.conf';
    private static $mod_ssl     = null;
    private static $mod_include = null;
    private static $ip          = null;

    public static function Init()
    {
        self::$httpdconf = self::$xampppath.DIRECTORY_SEPARATOR.'apache'.DIRECTORY_SEPARATOR.'conf'.DIRECTORY_SEPARATOR.'httpd.conf';
        self::$sslconf   = self::$xampppath.DIRECTORY_SEPARATOR.'apache'.DIRECTORY_SEPARATOR.'conf'.DIRECTORY_SEPARATOR.'extra'.DIRECTORY_SEPARATOR.'httpd-ssl.conf';

        self::mod_sslStatus();
        self::mod_includeStatus();
        self::ipStatus();

        return self::$functions;
    }

    private static function mod_sslStatus()
    {
        if (is_readable(self::$httpdconf)) {
            $confcontent = file_get_contents(self::$httpdconf);

            $matches = array();
            $confline = preg_quote('LoadModule ssl_module modules/mod_ssl.so', '|');
            if (!preg_match('|([#]*)?\\h*'.$confline.'|i', $confcontent, $matches)) {
                self::$functions['mod_ssl']['name']  = 'enable HTTPS (SSL)';
                self::$functions['mod_ssl']['value'] = 1;
                self::$mod_ssl = 0;
                return;
            }
            if (!empty($matches[1])) {
                self::$functions['mod_ssl']['name']  = 'enable HTTPS (SSL)';
                self::$functions['mod_ssl']['value'] = 1;
                self::$mod_ssl = 0;
                return;
            }

            self::$functions['mod_ssl']['name']  = 'disable HTTPS (SSL)';
            self::$functions['mod_ssl']['value'] = 0;
            self::$mod_ssl = 1;
            return;
        }

        unset(self::$functions['mod_ssl']);
        self::$mod_ssl = null;

        return;
    }

    public static function mod_ssl($switch)
    {
        $oldfileperm = fileperms(self::$httpdconf);
        if (!chmod(self::$httpdconf, 0666) && !is_writable(self::$httpdconf)) {
            throw new XAMPPException('File \''.self::$httpdconf.'\' is not writable.');
        }
        $confcontent = file_get_contents(self::$httpdconf);
        $confline    = preg_quote('LoadModule ssl_module modules/mod_ssl.so', '|');

        switch ($switch) {
        case 0:
            $confcontent = preg_replace('|\\h*('.$confline.')|i', '#\\1', $confcontent);
            break;
        case 1:
            if (!preg_match('|([#]*)?\\h*'.$confline.'|i', $confcontent)) {
                $confcontent .= PHP_EOL
                              . 'LoadModule ssl_module modules/mod_ssl.so'.PHP_EOL;
            } else {
                $confcontent = preg_replace('|[#]*\\h*('.$confline.')|i', '\\1', $confcontent);
            }
            break;
        }

        file_put_contents(self::$httpdconf, $confcontent);
        chmod(self::$httpdconf, $oldfileperm);

        return;
    }

    private static function mod_includeStatus()
    {
        if (is_readable(self::$httpdconf)) {
            $confcontent = file_get_contents(self::$httpdconf);

            $matches = array();
            $confline = preg_quote('LoadModule include_module modules/mod_include.so', '|');
            if (!preg_match('|([#]*)?\\h*'.$confline.'|i', $confcontent, $matches)) {
                self::$functions['mod_include']['name']  = 'enable Server Side Includes (SSI)';
                self::$functions['mod_include']['value'] = 1;
                self::$mod_ssl = 0;
                return;
            }
            if (!empty($matches[1])) {
                self::$functions['mod_include']['name']  = 'enable Server Side Includes (SSI)';
                self::$functions['mod_include']['value'] = 1;
                self::$mod_ssl = 0;
                return;
            }

            self::$functions['mod_include']['name']  = 'disable Server Side Includes (SSI)';
            self::$functions['mod_include']['value'] = 0;
            self::$mod_ssl = 1;
            return;
        }

        unset(self::$functions['mod_include']);
        self::$mod_include = null;

        return;
    }

    public static function mod_include($switch)
    {
        $oldfileperm = fileperms(self::$httpdconf);
        if (!chmod(self::$httpdconf, 0666) && !is_writable(self::$httpdconf)) {
            throw new XAMPPException('File \''.self::$httpdconf.'\' is not writable.');
        }
        $confcontent = file_get_contents(self::$httpdconf);

        switch ($switch) {
        case 0:
            $confline    = preg_quote('LoadModule include_module modules/mod_include.so', '|');
            $confcontent = preg_replace('|\\h*('.$confline.')|i', '#\\1', $confcontent);

            $confline    = preg_quote('AddType text/html .shtml', '|');
            $confcontent = preg_replace('|(\\h*'.$confline.')|i', '#\\1', $confcontent);

            $confline    = preg_quote('AddOutputFilter INCLUDES .shtml', '|');
            $confcontent = preg_replace('|(\\h*'.$confline.')|i', '#\\1', $confcontent);
            break;
        case 1:
            $confline    = preg_quote('LoadModule include_module modules/mod_include.so', '|');
            if (!preg_match('|([#]*)?\\h*'.$confline.'|i', $confcontent)) {
                $confcontent .= PHP_EOL
                              . 'LoadModule include_module modules/mod_include.so'.PHP_EOL
                              . 'AddType text/html .shtml'.PHP_EOL
                              . 'AddOutputFilter INCLUDES .shtml'.PHP_EOL;
            } else {
                $confcontent = preg_replace('|[#]*(\\h*'.$confline.')|i', '\\1', $confcontent);

                $confline    = preg_quote('AddType text/html .shtml', '|');
                $confcontent = preg_replace('|[#]*(\\h*'.$confline.')|i', '\\1', $confcontent);

                $confline    = preg_quote('AddOutputFilter INCLUDES .shtml', '|');
                $confcontent = preg_replace('|[#]*(\\h*'.$confline.')|i', '\\1', $confcontent);
            }
            break;
        }

        file_put_contents(self::$httpdconf, $confcontent);
        chmod(self::$httpdconf, $oldfileperm);

        return;
    }

    private static function ipStatus()
    {
        if (is_readable(self::$httpdconf)) {
            $confcontent = file_get_contents(self::$httpdconf);

            $matches = array();
            $confline = preg_quote('Listen 80', '|');
            if (!preg_match('|([#]*)?\\h*'.$confline.'|i', $confcontent, $matches)) {
                self::$ip = 0;
            } elseif (!empty($matches[1])) {
                self::$ip = 0;
            } else {
                self::$ip = 1;
            }

            if (0 == self::$ip) {
                $confline = preg_quote('Listen 0.0.0.0:80', '|');
                if (!preg_match('|([#]*)?\\h*'.$confline.'|i', $confcontent, $matches)) {
                } elseif (!empty($matches[1])) {
                } else {
                    self::$ip = 2;
                }
    
                $confline = preg_quote('Listen [::]:80', '|');
                if (!preg_match('|([#]*)?\\h*'.$confline.'|i', $confcontent, $matches)) {
                } elseif (!empty($matches[1])) {
                } else {
                    self::$ip += 4;
                }
            }

            switch (self::$ip) {
            case 1:
                self::$functions['ip']['name']  = 'enable IPv4 only (current: IPv4/6 (auto))';
                self::$functions['ip']['value'] = 2;
                break;
                
            case 2:
                self::$functions['ip']['name']  = 'enable IPv6 only (current: IPv4)';
                self::$functions['ip']['value'] = 4;
                break;
                
            case 4:
                self::$functions['ip']['name']  = 'enable IPv4/6 (current: IPv6)';
                self::$functions['ip']['value'] = 6;
                break;

            case 6:
                self::$functions['ip']['name']  = 'enable IPv4/6 (auto) (current: IPv4/6)';
                self::$functions['ip']['value'] = 1;
                break;

            default:
                self::$ip = 0;
                unset(self::$functions['ip']);
                break;
            }

            return;     
        }

        unset(self::$functions['ip']);
        self::$ip = null;

        return;
    }

    public static function ip($switch)
    {
        $oldfileperm = fileperms(self::$httpdconf);
        if (!chmod(self::$httpdconf, 0666) && !is_writable(self::$httpdconf)) {
            throw new XAMPPException('File \''.self::$httpdconf.'\' is not writable.');
        }
        $confcontent = file_get_contents(self::$httpdconf);

        switch ($switch) {
        case 1:
            $confline    = preg_quote('Listen 80', '|');
            $confcontent = preg_replace('|[#]*(\\h*'.$confline.')|i', '\\1', $confcontent);

            $confline    = preg_quote('Listen 0.0.0.0:80', '|');
            $confcontent = preg_replace('|[#]*(\\h*'.$confline.')|i', '#\\1', $confcontent);

            $confline    = preg_quote('Listen [::]:80', '|');
            $confcontent = preg_replace('|[#]*(\\h*'.$confline.')|i', '#\\1', $confcontent);
            break;
        case 2:
            $confline    = preg_quote('Listen 80', '|');
            $confcontent = preg_replace('|[#]*(\\h*'.$confline.')|i', '#\\1', $confcontent);

            $confline    = preg_quote('Listen 0.0.0.0:80', '|');
            $confcontent = preg_replace('|[#]*(\\h*'.$confline.')|i', '\\1', $confcontent);

            $confline    = preg_quote('Listen [::]:80', '|');
            $confcontent = preg_replace('|[#]*(\\h*'.$confline.')|i', '#\\1', $confcontent);
            break;
        case 4:
            $confline    = preg_quote('Listen 80', '|');
            $confcontent = preg_replace('|[#]*(\\h*'.$confline.')|i', '#\\1', $confcontent);

            $confline    = preg_quote('Listen 0.0.0.0:80', '|');
            $confcontent = preg_replace('|[#]*(\\h*'.$confline.')|i', '#\\1', $confcontent);

            $confline    = preg_quote('Listen [::]:80', '|');
            $confcontent = preg_replace('|[#]*(\\h*'.$confline.')|i', '\\1', $confcontent);
            break;
        case 6:
            $confline    = preg_quote('Listen 80', '|');
            $confcontent = preg_replace('|[#]*(\\h*'.$confline.')|i', '#\\1', $confcontent);

            $confline    = preg_quote('Listen 0.0.0.0:80', '|');
            $confcontent = preg_replace('|[#]*(\\h*'.$confline.')|i', '\\1', $confcontent);

            $confline    = preg_quote('Listen [::]:80', '|');
            $confcontent = preg_replace('|[#]*(\\h*'.$confline.')|i', '\\1', $confcontent);
            break;
        default:
            break;
        }

        file_put_contents(self::$httpdconf, $confcontent);
        chmod(self::$httpdconf, $oldfileperm);


        $oldfileperm = fileperms(self::$sslconf);
        if (!chmod(self::$sslconf, 0666) && !is_writable(self::$sslconf)) {
            throw new XAMPPException('File \''.self::$sslconf.'\' is not writable.');
        }
        $confcontent = file_get_contents(self::$sslconf);

        switch ($switch) {
        case 1:
            $confline    = preg_quote('Listen 443', '|');
            $confcontent = preg_replace('|[#]*(\\h*'.$confline.')|i', '\\1', $confcontent);

            $confline    = preg_quote('Listen 0.0.0.0:443', '|');
            $confcontent = preg_replace('|[#]*(\\h*'.$confline.')|i', '#\\1', $confcontent);

            $confline    = preg_quote('Listen [::]:443', '|');
            $confcontent = preg_replace('|[#]*(\\h*'.$confline.')|i', '#\\1', $confcontent);
            break;
        case 2:
            $confline    = preg_quote('Listen 443', '|');
            $confcontent = preg_replace('|[#]*(\\h*'.$confline.')|i', '#\\1', $confcontent);

            $confline    = preg_quote('Listen 0.0.0.0:443', '|');
            $confcontent = preg_replace('|[#]*(\\h*'.$confline.')|i', '\\1', $confcontent);

            $confline    = preg_quote('Listen [::]:443', '|');
            $confcontent = preg_replace('|[#]*(\\h*'.$confline.')|i', '#\\1', $confcontent);
            break;
        case 4:
            $confline    = preg_quote('Listen 443', '|');
            $confcontent = preg_replace('|[#]*(\\h*'.$confline.')|i', '#\\1', $confcontent);

            $confline    = preg_quote('Listen 0.0.0.0:443', '|');
            $confcontent = preg_replace('|[#]*(\\h*'.$confline.')|i', '#\\1', $confcontent);

            $confline    = preg_quote('Listen [::]:443', '|');
            $confcontent = preg_replace('|[#]*(\\h*'.$confline.')|i', '\\1', $confcontent);
            break;
        case 6:
            $confline    = preg_quote('Listen 443', '|');
            $confcontent = preg_replace('|[#]*(\\h*'.$confline.')|i', '#\\1', $confcontent);

            $confline    = preg_quote('Listen 0.0.0.0:443', '|');
            $confcontent = preg_replace('|[#]*(\\h*'.$confline.')|i', '\\1', $confcontent);

            $confline    = preg_quote('Listen [::]:443', '|');
            $confcontent = preg_replace('|[#]*(\\h*'.$confline.')|i', '\\1', $confcontent);
            break;
        default:
            break;
        }

        file_put_contents(self::$sslconf, $confcontent);
        chmod(self::$sslconf, $oldfileperm);

        return;
    }
}
?>
