<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_site = "localhost";
$database_site = "gestorlog";
$username_site = "log";
$password_site = "log";
$site = mysql_pconnect($hostname_site, $username_site, $password_site) or trigger_error(mysql_error(),E_USER_ERROR); 
?>