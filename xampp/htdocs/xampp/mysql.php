<?php
    ini_set('default_socket_timeout', 2);

    if (@mysql_connect("localhost", "pma", "")) {
        echo "OK";
    } else {
        $err = mysql_errno();
        if ((1044 == $err) || (1045 == $err) || (1130 == $err)) {
            echo "OK";
        } else {
            echo "NOK";
        }
    }
?>