<?php
    include "langsettings.php";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
    "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta name="author" content="Kai Oswald Seidler, Kay Vogelgesang, Carsten Wiedmann">
        <link href="xampp.css" rel="stylesheet" type="text/css">
        <script type="text/javascript" src="xampp.js"></script>
        <title></title>
    </head>

    <body class="n">
        <table border="0" cellpadding="0" cellspacing="0">
            <tr valign="top">
                <td align="right" class="navi">
                    <img src="img/blank.gif" alt="" width="145" height="15"><br>
                    <span class="nh"><?php echo trim(@file_get_contents('../../install/xampp_modell.txt')); ?></span><br>
                    <span class="navi">[PHP: <?php echo phpversion(); ?>]</span><br><br>
                </td>
            </tr>
            <tr>
                <td height="1" bgcolor="#fb7922" colspan="1" style="background-image:url(img/strichel.gif)" class="white"></td>
            </tr>
            <tr valign="top">
                <td align="right" class="navi">
                    <a name="start" id="start" class="n" target="content" onclick="h(this);" href="security.php"><?php echo $TEXT['navi-security']; ?></a><br><br>
                </td>
            </tr>
            <tr>
                <td bgcolor="#fb7922" colspan="1" class="white"></td>
            </tr>
            <tr valign="top">
                <td align="right" class="navi">
                    <br><span class="nh"><?php echo $TEXT['navi-languages']; ?></span><br>
                </td>
            </tr>
            <tr>
                <td height="1" bgcolor="#fb7922" colspan="1" style="background-image:url(img/strichel.gif)" class="white"></td>
            </tr>
            <tr valign="top">
                <td align="right" class="navi">
                    <a target=_parent class=n href="lang.php?en"><?php echo $TEXT['navi-english']; ?></a><br>
                    <a target=_parent class=n href="lang.php?de"><?php echo $TEXT['navi-german']; ?></a><br>
                    <a target=_parent class=n href="lang.php?fr"><?php echo $TEXT['navi-french']; ?></a><br>
                    <a target=_parent class=n href="lang.php?nl"><?php echo $TEXT['navi-dutch']; ?></a><br>
                    <a target=_parent class=n href="lang.php?pl"><?php echo $TEXT['navi-polish']; ?></a><br>
                    <a target=_parent class=n href="lang.php?sl"><?php echo $TEXT['navi-slovenian']; ?></a><br>
                    <a target=_parent class=n href="lang.php?it"><?php echo $TEXT['navi-italian']; ?></a><br>
                    <a target=_parent class=n href="lang.php?no"><?php echo $TEXT['navi-norwegian']; ?></a><br>
                    <a target=_parent class=n href="lang.php?es"><?php echo $TEXT['navi-spanish']; ?></a><br>
                    <a target=_parent class=n href="lang.php?zh"><?php echo $TEXT['navi-chinese']; ?></a><br>
                    <a target=_parent class=n href="lang.php?pt"><?php echo $TEXT['navi-portuguese']; ?></a><br>
                    <a target=_parent class=n href="lang.php?pt_br"><?php echo $TEXT['navi-portuguese-brasil']; ?></a><br>
                    <a target=_parent class=n href="lang.php?pt_br"><?php echo $TEXT['navi-japanese']; ?></a><br>
                </td>
            </tr>
            <tr>
                <td height="1" bgcolor="#fb7922" colspan="1" style="background-image:url(img/strichel.gif)" class="white"></td>
            </tr>
            <tr valign="top">
                <td align="right" class="navi">
                    <p class="navi">&copy;2002-2009<br>
                    <?php if (file_get_contents("../../install/xampp_language.txt") == "de") { ?>
                        <a target="_new" href="http://www.apachefriends.org/index.html"><img border="0" src="img/apachefriends.gif" alt=""></a><p>
                    <?php } else { ?>
                        <a target="_new" href="http://www.apachefriends.org/index-en.html"><img border="0" src="img/apachefriends.gif" alt=""></a><p>
                    <?php } ?>
                </td>
            </tr>
        </table>
    </body>
</html>
