:: Reset passwords for root and pma user
::
:: author     Carsten Wiedmann <carsten_sttgt@gmx.de>
:: copyright  2009 Carsten Wiedmann
:: license    http://www.freebsd.org/copyright/freebsd-license.html FreeBSD License
:: version    1.0
@ECHO OFF & SETLOCAL
PUSHD %~dp0
CD ..

ECHO USE `mysql`; >resetroot.sql
ECHO. >>resetroot.sql
ECHO INSERT IGNORE INTO `user` VALUES ('localhost', 'root', '', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', '', '', '', '', 0, 0, 0, 0); >>resetroot.sql
ECHO REPLACE INTO `user` VALUES ('localhost', 'root', '', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', '', '', '', '', 0, 0, 0, 0); >>resetroot.sql
ECHO INSERT IGNORE INTO `user` VALUES ('localhost', 'pma', '', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', 'Y', 'Y', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', '', '', '', '', 0, 0, 0, 0); >>resetroot.sql
ECHO REPLACE INTO `user` VALUES ('localhost', 'pma', '', 'N', 'N', 'N', 'N', 'N', 'N', 'Y', 'Y', 'Y', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', 'N', '', '', '', '', 0, 0, 0, 0); >>resetroot.sql

mysql\bin\mysqld.exe --no-defaults --bind-address=127.0.0.1 --bootstrap --console --skip-grant-tables --skip-innodb --standalone <resetroot.sql  >resetroot.err 2>&1
IF ERRORLEVEL 1 GOTO FEHLER
GOTO KEINFEHLER

:FEHLER
TYPE resetroot.err
ECHO.
ECHO Passwoerter fuer Benutzer "root" und "pma" wurden nicht geloescht!
ECHO Passwords for user "root" and "pma" were not deleted!
GOTO WEITER

:KEINFEHLER
ECHO.
ECHO Passwoerter fuer Benutzer "root" und "pma" wurden geloescht.
ECHO Passwords for user "root" and "pma" were deleted.
ECHO.
ECHO Bitte den MySQL Server neu starten.
ECHO Please restart the MySQL server.
GOTO WEITER

:WEITER
DEL resetroot.err >nul 2>&1
DEL resetroot.sql >nul 2>&1
ECHO.

POPD
PAUSE
