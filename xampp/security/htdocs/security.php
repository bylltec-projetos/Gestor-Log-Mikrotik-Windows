<?php
    include "langsettings.php";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
    "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta name="author" content="Kai Oswald Seidler, Kay Vogelgesang, Carsten Wiedmann">
        <link href="xampp.css" rel="stylesheet" type="text/css">
        <title><?php echo $TEXT['security-head']; ?> [Security Check 1.1]</title>
    </head>

    <body>
        <br><h1><?php echo $TEXT['security-head']; ?> [Security Check 1.1]</h1>
        <p><?php echo $TEXT['security-text1']; ?></p>

        <?php
            $i = 0;
            ini_set('default_socket_timeout', 2);

            function line($head, $textok, $info, $running, $notonload, $command) {
                $host = "127.0.0.1";
                global $i, $TEXT;
                list($partwampp, $directorwampp) = preg_split('|\\\security|', dirname(__FILE__));
                $htaccess = ".htaccess";
                $configinc = "config.inc.php";
                $tomcatusers = "tomcat-users.xml";
                $tomcatserver = "server.xml";

                $notrun = 0;
                $status = 0;
                $notload = 0;
                $newstatus = "nok";

                global $htxampp;
                global $phpmyadminconf;
                global $tomcatuserconf;
                global $tomcatserverconf;

                $htxampp = $partwampp."\htdocs\\xampp\\".$htaccess;
                $phpmyadminconf = $partwampp."\phpmyadmin\\".$configinc;
                if ($command == "phpmyadmin") {
                    if (file_exists($phpmyadminconf)) {
                        $datei = fopen($phpmyadminconf, 'r');
                        $status = 1;

                        while (!feof($datei)) {
                            $zeile = fgets($datei, 255);
                            @list($left, $right) = split('=', $zeile);
                            if (preg_match("/'auth_type'/i", $left)) {
                                if (preg_match("/'http'/i", $right)) {
                                    $newstatus = "ok";
                                } elseif (preg_match("/'cookie'/i", $right)) {
                                    $newstatus = "ok";
                                }
                                if ($newstatus == "ok") {
                                    $status = 0;
                                } else {
                                    $status = 1;
                                }
                            }
                        }
                        fclose($datei);
                    } else {
                        $notrun = 1;
                    }
                }


                $tomcatuserconf = $partwampp."\\tomcat\\conf\\".$tomcatusers;
                $tomcatserverconf = $partwampp."\\tomcat\\conf\\".$tomcatserver;
                if ($command == "tomcat") {
                    if (file_exists($tomcatserverconf)) {
                        if (file_exists($tomcatuserconf)) {
                            $datei = fopen($tomcatuserconf, 'r');
                            $status = 0;

                            while (!feof($datei)) {
                                $zeile = fgets($datei, 255);
                                if (preg_match("/username=\"xampp\"/i", $zeile)) {
                                    if (!preg_match("/password=\"xampp\"/i", $zeile)) {
                                        $newstatus = "ok";
                                    }
                                    if ($newstatus == "ok") {
                                        $status = 0;
                                    } else {
                                        $status = 1;
                                    }
                                }
                            }
                            fclose($datei);
                        } else {
                            $notrun = 1;
                        }
                    } else {
                        $running = '';
                        $status = 0;
                        $notrun = 1;
                        $notload = 1;
                    }
                }

                if ($command == "mysqlroot") {
                    if (false !== @fsockopen($host, 3306)) {
                        if (false !== @mysql_connect($host, "root", "")) {
                            $status = 1;
                        } else {
                            $status = 0;
                        }
                    } else {
                        $notrun = 1;
                    }
                }

                if ($command == "xampp") {
                    if (file_exists($htxampp)) {
                        $status = 0;
                    } else {
                        $status = 1;
                    }
                }

                if ($command == "php") {
                    if (ini_get('safe_mode')) {
                        $status = 0;
                    } else {
                        $status = 1;
                    }
                }

                if ($command == "ftp") {
                    if (false !== @fsockopen($host, 21)) {
                        if ((false === ($conn_id = ftp_connect($host))) ||
                            (false === @ftp_login($conn_id, "newuser", "wampp"))) {
                            $status = 0;
                        } else {
                            $status = 1;
                        }
                    } else {
                        $notrun = 1;
                    }
                }

                if ($command == "pop") {
                    if (extension_loaded("imap")) {
                        if (false !== @fsockopen($host, 110)) {
                            if (false !== @imap_open("{{$host}/pop3:110}INBOX", "newuser", "wampp")) {
                                $status = 1;
                            } else {
                                $status = 0;
                            }
                        } else {
                            $notrun = 1;
                        }
                    } else {
                        $running = '';
                        $status = 0;
                        $notrun = 1;
                        $notload = 1;
                    }
                }


                if ($i > 0) {
                    echo "<tr valign='bottom'>";
                    echo "<td bgcolor='#ffffff' height='1' style='background-image:url(img/strichel.gif)' colspan='4'></td>";
                    echo "</tr>";
                }

                echo "<tr bgcolor='#ffffff' valign='middle'><td><img src='img/blank.gif' alt='' width='1' height='20'></td><td class='tabval'>";
                if ($notload == 1) {
                    echo $notonload;
                }
                if (($status == 0) && (($notrun == "") || ($notrun < 1))) {
                    echo $textok;
                } elseif ($notrun == 1) {
                    echo $running;
                } else {
                    echo $head;
                }

                echo "</td>";
                if (($status == 0) && ($notrun != 1)) {
                    echo "<td>&nbsp;&nbsp;<span class='green'>&nbsp;".$TEXT['security-ok']."&nbsp;</span></td>";
                } elseif ($status == 1) {
                    echo "<td>&nbsp;&nbsp;<span class='red'>&nbsp;".$TEXT['security-nok']."&nbsp;</span></td>";
                } elseif ($notrun == 1) {
                    echo "<td>&nbsp;&nbsp;<span class='yellow'>&nbsp;".$TEXT['security-noidea']."&nbsp;</span></td>";
                } else {
                    echo "<td>&nbsp;&nbsp;<span class='yellow'>&nbsp;".$TEXT['security-noidea']."&nbsp;</span></td>";
                }
                echo "<td>&nbsp;</td></tr>";

                if ($notrun == 1 && $notload =! 1) {
                    echo "<tr bgcolor='#ffffff'><td></td><td colspan='1' class='small'>$running<br><img src='img/blank.gif' alt='' width='10' height='10' border='0'></td><td></td><td></td></tr>";
                } elseif ($status) {
                    echo "<tr bgcolor='#ffffff'><td></td><td colspan='1' class='small'>$info<br><img src='img/blank.gif' alt='' width='10' height='10' border='0'></td><td></td><td></td></tr>";
                }

                $i++;
            }

            echo "<table border='0' cellpadding='0' cellspacing='0'>";
            echo "<tr valign='top'>";
            echo "<td bgcolor='#fb7922' valign='top'><img src='img/blank.gif' alt='' width='10' height='0'></td>";
            echo "<td bgcolor='#fb7922' class='tabhead'><img src='img/blank.gif' alt='' width='250' height='6'><br>".$TEXT['security-tab1']."</td>";
            echo "<td bgcolor='#fb7922' class='tabhead'><img src='img/blank.gif' alt='' width='100' height='6'><br>".$TEXT['security-tab2']."</td>";
            echo "<td bgcolor='#fb7922' valign='top'><br><img src='img/blank.gif' alt='' width='1' height='10'></td>";
            echo "</tr>";

            line($TEXT['security-checkapache-nok'], $TEXT['security-checkapache-ok'], $TEXT['security-checkapache-text'], "", "", "xampp");

            line($TEXT['security-checkmysql-nok'], $TEXT['security-checkmysql-ok'], $TEXT['security-checkmysql-text'], $TEXT['security-checkmysql-out'], "", "mysqlroot");

            line($TEXT['security-phpmyadmin-nok'], $TEXT['security-phpmyadmin-ok'], $TEXT['security-phpmyadmin-text'], $TEXT['security-phpmyadmin-out'], "", "phpmyadmin");

            line($TEXT['security-checkftppassword-nok'], $TEXT['security-checkftppassword-ok'], $TEXT['security-checkftppassword-text'], $TEXT['security-checkftppassword-out'], "", "ftp");

            // line($TEXT['security-checkphp-nok'], $TEXT['security-checkphp-ok'], $TEXT['security-checkphp-text'], $TEXT['security-checkphp-out'], "", "php");

            line($TEXT['security-pop-nok'], $TEXT['security-pop-ok'], $TEXT['security-pop-text'], $TEXT['security-pop-out'], $TEXT['security-pop-notload'], "pop");

            line($TEXT['security-checktomcat-nok'], $TEXT['security-checktomcat-ok'], $TEXT['security-checktomcat-text'], $TEXT['security-checktomcat-out'], $TEXT['security-checktomcat-notinstalled'], "tomcat");

            echo "<tr valign='bottom'>";
            echo "<td bgcolor='#fb7922'></td>";
            echo "<td bgcolor='#fb7922' colspan='3'><img src='img/blank.gif' alt='' width='1' height='8'></td>";
            echo "<td bgcolor='#fb7922'></td>";
            echo "</tr>";

            echo "</table>";
            echo "<p>";
        ?>
        <?php echo $TEXT['security-text2']; ?><p>
        <?php echo $TEXT['security-text3']; ?><br>&nbsp;<p>
        <?php echo $TEXT['security-text4']; ?>

        <p>
        <table border="0">
            <tr>
                <td>ftp</td>
                <td>&nbsp;</td>
                <td><b>21</b>/tcp</td>
                <td>&nbsp;</td>
                <td># File Transfer [Control] (XAMPP: FTP Default Port)</td>
            </tr>
            <tr>
                <td>smtp</td>
                <td>&nbsp;</td>
                <td><b>25</b>/tcp</td>
                <td>&nbsp;</td>
                <td>mail # Simple Mail Transfer (XAMPP: SMTP Default Port)</td>
            </tr>
            <tr>
                <td>http</td>
                <td>&nbsp;</td>
                <td><b>80</b>/tcp</td>
                <td>&nbsp;</td>
                <td># World Wide Web HTTP (XAMPP: Apache Default Port)</td>
            </tr>
            <tr>
                <td>pop3</td>
                <td>&nbsp;</td>
                <td><b>110</b>/tcp</td>
                <td>&nbsp;</td>
                <td># Post Office Protocol - Version 3 (XAMPP: POP3 Default Port)</td>
            </tr>
            <tr>
                <td>imap</td>
                <td>&nbsp;</td>
                <td><b>143</b>/tcp</td>
                <td>&nbsp;</td>
                <td># Internet Message Access Protocol (XAMPP: IMAP Default Port)</td>
            </tr>

            <tr>
                <td>https</td>
                <td>&nbsp;</td>
                <td><b>443</b>/tcp</td>
                <td>&nbsp;</td>
                <td># http protocol over TLS/SSL (XAMPP: Apache SSL Port)</td>
            </tr>
            <tr>
                <td>mysql</td>
                <td>&nbsp;</td>
                <td><b>3306</b>/tcp</td>
                <td>&nbsp;</td>
                <td># MySQL (XAMPP: MySQL Default Port)</td>
            </tr>
            <tr>
                <td>AJP/1.3</td>
                <td>&nbsp;</td>
                <td><b>8009</b></td>
                <td>&nbsp;</td>
                <td># AJP/1.3 (XAMPP: Tomcat AJP/1.3 Port)</td>
            </tr>
            <tr>
                <td>http-alt</td>
                <td>&nbsp;</td>
                <td><b>8080</b>/tcp</td>
                <td>&nbsp;</td>
                <td># HTTP Alternate (see port 80) (XAMPP: Tomcat Default Port)</td>
            </tr>
        </table>
        <!--
        smtp          25/tcp   # Simple Mail Transfer (XAMPP: SMTP Default Port)
        http          80/tcp   # World Wide Web HTTP (XAMPP: Apache Default Port)
        pop3         110/tcp   # Post Office Protocol - Version 3 (XAMPP: POP3 Default Port)
        imap         143/tcp   # Internet Message Access Protocol (XAMPP: IMAP Default Port)
        https        443/tcp   # http protocol over TLS/SSL (XAMPP: Apache SSL Port)
        mysql       3306/tcp   # MySQL (XAMPP: MySQL Default Port)
        AJP/1.3     8009/tcp   # AJP/1.3 (XAMPP: Tomcat AJP/1.3 Port)
        http-alt    8080/tcp   # HTTP Alternate (see port 80) (XAMPP: Tomcat Default Port)
        -->
    </body>
</html>
