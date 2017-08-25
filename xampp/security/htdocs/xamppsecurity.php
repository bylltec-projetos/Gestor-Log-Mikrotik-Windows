<?php
    include "langsettings.php";
    include "securefunctions.php";

    extract($_POST);
    extract($_SERVER);

    $host = "127.0.0.1";
    ini_set('default_socket_timeout', 1);

    list($partwampp, $directorwampp) = preg_split('|\\\security\\\htdocs|', dirname(__FILE__));
    $mypasswdtxt = "mysqlrootpasswd.txt";
    $xapasswdtxt = "xamppdirpasswd.txt";
    $mypasswdtxtdir = $partwampp."\security\\".$mypasswdtxt;
    $xapasswdtxtdir = $partwampp."\security\\".$xapasswdtxt;

    if (false !== @fsockopen($host, 3306)) {
        if(false !== @mysql_connect($host, "root", "")) {
            $registpasswd = "no";
            mysql_close();
        } else {
            $registpasswd = "yes";
        }
        $mysqlrun = 1;
    } else {
        $mysqlrun = 0;
    }

    if ($xamppaccess) {
        if (preg_match('/^[a-zA-Z0-9@*#]{3,15}$/', $xamppuser)) {
            $xamppereg = "ok";
        } else {
            $xamppereg = "notok";
        }
        if (preg_match('/^[a-zA-Z0-9@*#]{3,15}$/', $xampppasswd)) {
            $xampperegpass = "ok";
        } else {
            $xampperegpass = "notok";
        }
        if (($xamppereg == "ok") && ($xampperegpass == "ok")) {
            htaccess($xamppuser, $xampppasswd);
        }
    }

    if ($changing) {
        if (preg_match('/^[a-zA-Z0-9@*#]{3,15}$/', $mypasswd)) {
            $mysqlpasswdereg = "ok";
        } else {
            $mysqlpasswdereg = "notok";
        }
        if (preg_match('/^[a-zA-Z0-9@*#]{3,15}$/', $mypasswdrepeat)) {
            $remysqlpasswdereg = "ok";
        } else {
            $remysqlpasswdereg = "notok";;
        }
        if (($mysqlpasswdereg == "ok") && ($remysqlpasswdereg == "ok")) {
        }
        if ($mypasswdold) {
            if (preg_match('/^[a-zA-Z0-9@*#]{3,15}$/', $mypasswdold)) {
                $oldmysqlpasswdereg = "ok";
            } else {
                $oldmysqlpasswdereg = "notok";
            }
            if (($mysqlpasswdereg == "ok") && ($remysqlpasswdereg == "ok") && ($oldmysqlpasswdereg == "ok")) {
                $mysqlpassok = "yes";
            }
        } else {
            if (($mysqlpasswdereg == "ok") && ($remysqlpasswdereg == "ok")) {
                $mysqlpassok = "yes";
            }
        }
        if ($mysqlpassok == "yes") {
            if ($mypasswd != $mypasswdrepeat) {
            } else {
                mysqlrootupdate($mypasswdold, $mypasswd, $mypasswdrepeat);
                if ($rootpasswdupdate == "yes") {
                    phpmyadminstatus();
                    if (($currentstatus[0] == "cookie") || ($currentstatus[0] == "http")) {
                        if ($currentstatus[0] == $authphpmyadmin) {
                        } else {
                            changephpadminauth($authphpmyadmin, "1");
                        }
                    } else {
                        changephpadminauth($authphpmyadmin, "0");
                    }

                    if ($authpmauser == "1") {
                        mysqlpmaupdate();
                    }
                    if ($pmapasswdupdate == "yes") {
                       changephpadminpma(); 
                    }
                } else {
                    $mysqlpassok = "no";
                }
            }
        }
    }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
    "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta name="author" content="Kai Oswald Seidler, Kay Vogelgesang, Carsten Wiedmann">
        <link href="xampp.css" rel="stylesheet" type="text/css">
        <title><?php echo $TEXT['mysql-security-head']; ?></title>
    </head>

    <body>
        <br>
        <form method="post" action="<?php echo $PHP_SELF; ?>">
            <table width="600" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td align="left" width="600" colspan="2"><hr width="80%" style="border: solid #bb3902 1px; height: 1px"></td>
                </tr>
                <tr>
                    <td align="left" width="200">&nbsp;</td>
                    <td align="left" width="400">&nbsp;</td>
                </tr>
                <tr>
                    <td align="left" width="600" colspan="2"><h1><?php echo $TEXT['mysql-security-head']; ?></h1></td>
                </tr>
                <tr>
                    <td align="left" width="600" colspan="2"><hr width="80%" style="border: solid #bb3902 1px; height: 1px"></td>
                </tr>
                <tr>
                    <td align="left" width="200">&nbsp;</td>
                    <td align="left" width="400">&nbsp;</td>
                </tr>
                <tr>
                    <td align="left" width="600" colspan="2"><b><?php echo $TEXT['mysql-rootsetup-head']; ?></b></td>
                </tr>
                <tr>
                    <td align="left" width="600" colspan="2">
                        <?php
                            if ($changing && ($mysqlpassok != "yes")) {
                                echo "<b><i><font color=\"#FF3366\">".$TEXT['xampp-setup-notok']."</font></i></b>";
                            }
                            if ($changing && ($mysqlpassok == "yes")) {
                                echo "<b><i><font color=\"#000000\">".$TEXT['xampp-setup-ok']."</font></i></b>";
                            }
                        ?>
                        &nbsp;
                    </td>
                </tr>
                <tr>
                    <td align="left" width="200">MySQL SuperUser:</td>
                    <td align="left" width="400"><b>root</b></td>
                </tr>
                <tr>
                    <td align="left" width="200">&nbsp;</td>
                    <td align="left" width="400">&nbsp;</td>
                </tr>

                <?php
                    if ($mysqlrun == 0) {
                        echo "<tr><td align=\"left\" width=\"600\" colspan=\"2\"><h3><i>".$TEXT['mysql-rootsetup-notrunning']."</i></h3></td></tr>\n";
                    } else {
                        if ($update == "yes"){
                            echo "<tr><td align=\"left\" width=\"200\">&nbsp;</td><td align=\"left\" width=\"400\">&nbsp;</td></tr>\n";
                            echo "<tr><td align=\"left\" width=\"600\" colspan=\"2\"><font color=\"#0000A0\"><b>".$TEXT['mysql-rootsetup-passwdsuccess']."<br><br>$mypasswdtxtdir";
                            if ($phpmyadminconfsafe) {
                                echo "<br>$phpmyadminconfsafe";
                            }
                            echo "</font></b></td></td></tr>\n";
                            echo "<tr><td align=\"left\" width=\"200\">&nbsp;</td><td align=\"left\" width=\"400\">&nbsp;</td></tr>\n";
                        }
                        if ($update == "no") {
                            echo "<tr><td align=\"left\" width=\"200\">&nbsp;</td><td align=\"left\" width=\"400\">&nbsp;</td></tr>\n";
                            echo "<tr><td align=\"left\" width=\"600\" colspan=\"2\"><h3><i>".$TEXT['mysql-rootsetup-passwdnosuccess']."</i></h3></td></td></tr>\n";
                            echo "<tr><td align=\"left\" width=\"200\">&nbsp;</td><td align=\"left\" width=\"400\">&nbsp;</td></tr>\n";
                        }
                        if ($mypasswdok == "null") {
                            echo "<tr><td align=\"left\" width=\"200\">&nbsp;</td><td align=\"left\" width=\"400\">&nbsp;</td></tr>\n";
                            echo "<tr><td align=\"left\" width=\"600\" colspan=\"2\"><h3><i>".$TEXT['mysql-rootsetup-passwdnull']."</i></h3></td></td></tr>\n";
                            echo "<tr><td align=\"left\" width=\"200\">&nbsp;</td><td align=\"left\" width=\"400\">&nbsp;</td></tr>\n";
                        }
                        // &nbsp;&nbsp;<input type=\"checkbox\" name=\"mysqlpasswordfile\" value=\"yes\">
                        if ($mypasswdok == "no") {
                            echo "<tr><td align=\"left\" width=\"200\">&nbsp;</td><td align=\"left\" width=\"400\">&nbsp;</td></tr>\n";
                            echo "<tr><td align=\"left\" width=\"600\" colspan=\"2\"><h3><i>".$TEXT['mysql-rootsetup-passwdnotok']."</i></h3></td></td></tr>\n";
                            echo "<tr><td align=\"left\" width=\"200\">&nbsp;</td><td align=\"left\" width=\"400\">&nbsp;</td></tr>\n";
                        }
                        if ($registpasswd == "yes") {
                            echo "<tr><td align=\"left\" width=\"200\">".$TEXT['mysql-rootsetup-passwdold']."</td><td align=\"left\" width=\"400\"><input type=\"password\" name=\"mypasswdold\" size=\"40\"></td></tr>\n";
                        }

                        echo "<tr><td align=\"left\" width=\"200\">".$TEXT['mysql-rootsetup-passwd']."</td><td align=\"left\" width=\"400\"><input type=\"password\" name=\"mypasswd\" size=\"40\"></td></tr>\n";
                        echo "<tr><td align=\"left\" width=\"200\">".$TEXT['mysql-rootsetup-passwdrepeat']."</td><td align=\"left\" width=\"400\"><input type=\"password\" name=\"mypasswdrepeat\" size=\"40\"> </td></tr>\n";
                        echo "<tr><td align=\"left\" width=\"200\">&nbsp;</td><td align=\"center\" width=\"400\">&nbsp;</td></tr>\n";
                        if ($authphpmyadmin) {
                            if ($authphpmyadmin == "cookie") {
                                $checkedcookie = "checked";
                            } elseif ($currentstatus[0] == "http") {
                                $checkedhttpd = "checked";
                            } else {
                                $checkedcookie = "checked";
                            }
                        } else {
                            phpmyadminstatus();
                            if ($currentstatus[0] == "cookie") {
                                $checkedcookie = "checked";
                            } elseif ($currentstatus[0] == "http") {
                                $checkedhttpd = "checked";
                            } else {
                                $checkedcookie = "checked";
                            }
                        }
                        if (('' == $authpmauser) || ('1' == $authpmauser)) {
                            $checkedpmauseryes = "checked";
                        } else {
                            $checkedpmauserno = "checked";
                        }

                        echo "<tr><td align=\"left\" width=\"200\">".$TEXT['mysql-rootsetup-phpmyadmin']."</td><td align=\"left\" width=\"400\"><i>http</i> <input type=\"radio\" value=\"http\" $checkedhttpd name=\"authphpmyadmin\">&nbsp;&nbsp;<i>cookie</i> <input type=\"radio\" value=\"cookie\" $checkedcookie name=\"authphpmyadmin\"></td></tr>\n";
                        echo "<tr><td align=\"left\" width=\"200\">&nbsp;</td><td align=\"center\" width=\"400\">&nbsp;</td></tr>\n";
                        echo "<tr><td align=\"left\" width=\"200\">".$TEXT['mysql-pmasetup']."</td><td align=\"left\" width=\"400\"><i>".$TEXT['mysql-pmasetup-yes']."</i> <input type=\"radio\" value=\"1\" $checkedpmauseryes name=\"authpmauser\">&nbsp;&nbsp;<i>".$TEXT['mysql-pmasetup-no']."</i> <input type=\"radio\" value=\"0\" $checkedpmauserno name=\"authpmauser\"></td></tr>\n";
                        echo "<tr><td align=\"left\" width=\"200\">&nbsp;</td><td align=\"center\" width=\"400\">&nbsp;</td></tr>\n";
                        echo "<tr><td align=\"left\" width=\"*\" colspan=\"2\">---- ".$TEXT['mysql-passwort-risk']." ----</td></tr>\n";
                        echo "<tr><td align=\"left\" width=\"*\" colspan=\"2\">".$TEXT['mysql-passwort-infile']."&nbsp;&nbsp;<input type=\"checkbox\" name=\"mysqlpfile\" value=\"yes\"></td></tr>\n";
                        echo "<tr><td align=\"left\" width=\"*\" colspan=\"2\">(File: $mypasswdtxtdir)</td></tr>\n";
                        echo "<tr><td align=\"center\" width=\"*\" colspan=\"2\">&nbsp;</td></tr>";
                        // echo "<tr><td align=\"left\" width=\"200\">&nbsp;</td><td align=\"center\" width=\"400\">&nbsp;</td></tr>\n";
                        echo "<tr><td align=\"left\" width=\"200\">&nbsp;</td><td align=\"left\" width=\"400\"><input type=\"submit\" value=\"".$TEXT['mysql-rootsetup-passwdchange']."\" name=\"changing\"></td></tr>";
                    }
                    echo "<tr><td align=\"left\" width=\"200\">&nbsp;</td><td align=\"center\" width=\"400\">&nbsp;</td></tr>\n";
                    echo "<tr><td align=\"left\" width=\"600\" colspan=\"2\"><hr width=\"80%\" style=\"border: solid #bb3902 1px; height: 1px\"></td></tr>\n";
                    echo "<tr><td align=\"left\" width=\"600\" colspan=\"2\">&nbsp;";
                    if (($xamppereg == "notok") || ($xampperegpass == "notok")) {
                        echo "<b><i><font color=\"#FF3366\">".$TEXT['xampp-setup-notok']."</font></i></b>";
                    }
                    if ($xamppdirconfig == "notok") {
                        echo "<b><i><font color=\"#FF3366\">".$TEXT['xampp-config-notok']."</font></i></b>";
                        }
                    if ($xamppdirconfig == "ok") {
                        echo "<b><font color=\"#0000A0\">".$TEXT['xampp-config-ok']."$htpasswddirectory<br>$htxampp";
                        if ($xapfile == "yes") {
                            echo "<br>$xapasswdtxtdir</font></b><br><br>";
                        } else {
                            echo "</font></b><br><br>";
                        }
                    }
                    echo "&nbsp;</td></tr>\n";
                    echo "<tr><td align=\"left\" width=\"600\" colspan=\"2\"><b>".$TEXT['xampp-setup-head']."</b></td></tr>\n";
                    echo "<tr><td align=\"left\" width=\"200\">&nbsp;</td><td align=\"center\" width=\"400\">&nbsp;</td></tr>\n";
                    echo "<tr><td align=\"left\" width=\"200\">".$TEXT['xampp-setup-user']."</td><td align=\"left\" width=\"400\"><input type=\"text\" name=\"xamppuser\" size=\"40\"></td></tr>\n";
                    echo "<tr><td align=\"left\" width=\"200\">".$TEXT['xampp-setup-passwd']."</td><td align=left width=\"400\"><input type=\"password\" name=\"xampppasswd\" size=\"40\"></td></tr>\n";
                    echo "<tr><td align=\"center\" width=\"600\" colspan=2>&nbsp;</td></tr>\n";
                    echo "<tr><td align=\"left\" width=\"*\" colspan=\"2\">---- ".$TEXT['mysql-passwort-risk']." ----</td></tr>\n";
                    echo "<tr><td align=\"left\" width=\"*\" colspan=\"2\">".$TEXT['mysql-passwort-infile']."&nbsp;&nbsp;<input type=\"checkbox\" name=\"xapfile\" value=\"yes\"></td></tr>\n";
                    echo "<tr><td align=\"left\" width=\"*\" colspan=\"2\">(File: $xapasswdtxtdir)</td></tr>\n";
                    echo "<tr><td align=\"center\" width=\"*\" colspan=\"2\">&nbsp;</td></tr>\n";
                    echo "<tr><td align=\"left\" width=\"200\">&nbsp;</td><td align=\"left\" width=\"400\"><input type=\"submit\" value=\"".$TEXT['xampp-setup-start']."\" name=\"xamppaccess\"></td></tr>\n";
                ?>
            </table>
        </form>
    </body>
</html>
