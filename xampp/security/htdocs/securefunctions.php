<?php
    function mysqlrootupdate($currentpass, $newpass, $renewpass) {
        list($partwampp, $directorwampp) = preg_split('|\\\security\\\htdocs|', dirname(__FILE__));
        $mypasswdtxt = "mysqlrootpasswd.txt";
        $mypasswdtxtdir = $partwampp."\security\\".$mypasswdtxt;
        $dir = $partwampp."\security\\";
        global $rootpasswdupdate;
        global $update;
        global $mysqlpfile;
        if (false !== @mysql_connect("localhost", "root", $currentpass)) {
            mysql_select_db("mysql");

            mysql_query("SET PASSWORD = PASSWORD('".mysql_real_escape_string($newpass)."')");  //Fix by Wiedmann
            mysql_query("FLUSH PRIVILEGES");
            mysql_close();

            if (!file_exists($dir)) { // Fix by Wiedmann
                mkdir($dir);
            }
            if ($mysqlpfile=="yes")
            {
            $datei = fopen($mypasswdtxtdir, 'w');
            $put = "mysql user = root\r\nmysql password = $newpass\r\n";
            fputs($datei, $put);
            fclose($datei);
            }
            $rootpasswdupdate = "yes";
        } else {
            $rootpasswdupdate = "no";
        }
    }

    function mysqlpmaupdate() {
        list($partwampp, $directorwampp) = preg_split('|\\\security\\\htdocs|', dirname(__FILE__));
        $mypasswdtxt = "mysqlrootpasswd.txt";
        $mypasswdtxtdir = $partwampp."\security\\".$mypasswdtxt;
        $dir = $partwampp."\security\\";
        global $pmapasswdupdate;
        global $update;
        global $mysqlpfile;
        global $pmanewpass;
        global $mypasswd;

        $pmanewpass = uniqid();

        if (false !== @mysql_connect("localhost", "root", $mypasswd)) {
            mysql_select_db("mysql");

            mysql_query("SET PASSWORD for 'pma'@'localhost' = PASSWORD('".mysql_real_escape_string($pmanewpass)."')");  //Fix by Wiedmann
            mysql_query("FLUSH PRIVILEGES");
            mysql_close();

            if (!file_exists($dir)) { // Fix by Wiedmann
                mkdir($dir);
            }
            if ($mysqlpfile=="yes")
            {
            $datei = fopen($mypasswdtxtdir, 'a');
            $put = "\r\nphpmyadmin user = pma\r\nmysql password = $pmanewpass\r\n";
            fputs($datei, $put);
            fclose($datei);
            }
            $pmapasswdupdate = "yes";
        } else {
            $pmapasswdupdate = "no";
        }
    }

    function phpmyadminstatus() {
        global $currentstatus;
        global $authzeile;
        global $notfind;

        list($partwampp, $directorwampp) = preg_split('|\\\htdocs|', dirname(__FILE__));
        $configinc = "config.inc.php";
        $phpmyadminconf = $partwampp."\phpmyadmin\\".$configinc;

        if (file_exists($phpmyadminconf)) {
            $datei = fopen($phpmyadminconf, 'r');
            $i = 0;
            while (!feof($datei)) {
                $zeile = fgets($datei, 255);
                $oldzeile[] = $zeile;
                @list($left, $right) = split('=', $zeile);
                if (preg_match("/'auth_type'/i", $left)) {
                    if (preg_match("/'http'/i", $right)) {
                        $currentstatus[] = "http";
                        $authzeile[] = $i;
                    } elseif (preg_match("/'cookie'/i", $right)) {
                        $currentstatus[] = "cookie";
                        $authzeile[] = $i;
                    } else {
                        $currentstatus[] = "null";
                        $authzeile[] = $i;
                    }
                }
                $i++;
            }
            fclose($datei);
        } else {
            $notfind = 1;
        }
    }

    function changephpadminauth($phpmyadminauth, $myupdate) {
        global $phpmyadminconfsafe;
        list($partwampp, $directorwampp) = preg_split('|\\\security\\\htdocs|', dirname(__FILE__));
        $configinc = "config.inc.php";
        $phpmyadminconf = $partwampp."\phpmyadmin\\".$configinc;

        if (file_exists($phpmyadminconf)) {
            $datei = fopen($phpmyadminconf, 'r');
            $i = 0;
            while (!feof($datei)) {
                $zeile = fgets($datei, 255);
                $oldzeile[] = $zeile;
                @list($left, $right) = split('=', $zeile);
                if (preg_match("/'auth_type'/i", $left)) {
                    if (preg_match("/'http'/i", $right)) {
                        $currentstatus[] = "http";
                        $authzeile[] = $i;
                    } elseif (preg_match("/'cookie'/i", $right)) {
                        $currentstatus[] = "cookie";
                        $authzeile[] = $i;
                    } else {
                        $currentstatus[] = "null";
                        $authzeile[] = $i;
                    }
                }
                $i++;
            }
            fclose($datei);
        } else {
            $notfind = 1;
        }

        $mynewzeile = "\$cfg['Servers'][\$i]['auth_type']            = '$phpmyadminauth'; /* Authentication method (config, http or cookie based) */\r\n";

        if (file_exists($phpmyadminconf)) {
            if (($currentstatus[0] == "null") || ($myupdate == "1")) {
                copy($phpmyadminconf, $phpmyadminconf.'.safe');
                $phpmyadminconfsafe = $partwampp."\phpmyadmin\\".$configinc.".safe";
                $phpmyadminauth = "http";
                $datei = fopen($phpmyadminconf, 'w');
                if ($datei) {
                    for ($z = 0; $z < $i + 1; $z++) {
                        if ($authzeile[0] == $z) {
                            fputs($datei, $mynewzeile);
                        } else {
                            fputs($datei, $oldzeile[$z]);
                        }
                    }
                }
            }
        }
    }

    function changephpadminpma() {
        global $pmanewpass;
        list($partwampp, $directorwampp) = preg_split('|\\\security\\\htdocs|', dirname(__FILE__));
        $configinc = "config.inc.php";
        $phpmyadminconf = $partwampp."\phpmyadmin\\".$configinc;

        if (file_exists($phpmyadminconf)) {
            $datei = file_get_contents($phpmyadminconf);
            $datei = preg_replace('/(\$cfg\[\'Servers\'\]\[\$i\]\[\'controlpass\'\]\h*?=\h*?\').*?(\';)/i', '${1}'.$pmanewpass.'${2}', $datei);
            file_put_contents($phpmyadminconf, $datei);
        } else {
            $notfind = 1;
        }
    }

    function htaccess($xauser, $xapasswd) {
        global $xamppdirconfig;
        global $xapasswdtxtdir;
        global $htpasswddir;
        global $htpasswddirectory;
        global $htxampp;
        global $xapfile;
        list($partwampp, $directorwampp) = preg_split('|\\\security\\\htdocs|', dirname(__FILE__));
        $htaccess = ".htaccess";
        $xapasswdtxt = "xamppdirpasswd.txt";
        $htpasswd = "xampp.users";
        $xapasswdtxtdir = $partwampp."\security\\".$xapasswdtxt;
        $curspcript = $_SERVER["SCRIPT_FILENAME"];
        $htxampp = $partwampp."\htdocs\\xampp\\".$htaccess;
        $htpasswdexe = $partwampp."\apache\\bin\\htpasswd.exe";
        if (file_exists($htpasswdexe)) {
            $htpasswddir = "\"".$partwampp."\security\\".$htpasswd."\"";
            $htpasswddirectory = $partwampp."\security\\".$htpasswd;
            $dir = $partwampp."\security\\";
            if (!file_exists($dir)) {
                mkdir($dir);
            }

            $datei = fopen($htxampp, 'w+');
            $insert = "AuthName \"xampp user\"\r\nAuthType Basic\r\nAuthUserFile $htpasswddir\r\nrequire valid-user";
            fputs($datei,$insert);
            fclose($datei);

            $curdir=getcwd();
            chdir($partwampp."\security"); // Fix by Wiedmann
            $htpassrealm = "start /b \"\" \"$htpasswdexe\" -c -m -b .\\xampp.users $xauser $xapasswd";
            $handle = popen($htpassrealm, 'w');
            pclose($handle);
            chdir($curdir);
            if ($xapfile=="yes")    {
                $datei = fopen($xapasswdtxtdir, 'w+');
                $put = "XAMPP user = $xauser\r\nXAMPP password = $xapasswd";
                fputs($datei, $put);
                fclose($datei);
            }
            $hdir = $dir.'htdocs\\';
            copy($htxampp, $dir.'htdocs\\.htaccess');
            $xamppdirconfig = "ok";
        } else {
            $xamppdirconfig = "notok";
        }
    }
?>
