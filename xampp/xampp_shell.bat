@ECHO OFF

GOTO weiter
:setenv
SET "MIBDIRS=%~dp0php\extras\mibs"
SET "MIBDIRS=%MIBDIRS:\=/%"
SET "MYSQL_HOME=%~dp0mysql\bin"
SET "OPENSSL_CONF=%~dp0apache\bin\openssl.cnf"
SET "OPENSSL_CONF=%OPENSSL_CONF:\=/%"
SET "PHP_PEAR_SYSCONF_DIR=%~dp0php"
SET "PHPRC=%~dp0php"
SET "TMP=%~dp0tmp"
SET "PERL5LIB="
SET "Path=%~dp0;%~dp0php;%~dp0perl\site\bin;%~dp0perl\bin;%~dp0apache\bin;%~dp0mysql\bin;%~dp0FileZillaFTP;%~dp0MercuryMail;%~dp0sendmail;%~dp0webalizer;%~dp0tomcat\bin;%Path%"
GOTO :EOF
:weiter

IF "%1" EQU "setenv" (
    ECHO.
    ECHO Setting environment for using XAMPP for Windows.
    CALL :setenv
) ELSE (
    SETLOCAL
    TITLE XAMPP for Windows
    PROMPT %username%@%computername%$S$P$_#$S
    START "" /B %COMSPEC% /K "%~f0" setenv
)
