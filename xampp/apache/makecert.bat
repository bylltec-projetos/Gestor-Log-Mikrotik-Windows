:: Make a selfsigend certificate with OpenSSL
::
:: author     Carsten Wiedmann <carsten_sttgt@gmx.de>
:: copyright  2009 Carsten Wiedmann
:: license    http://www.freebsd.org/copyright/freebsd-license.html FreeBSD License
:: version    1.0
@ECHO OFF & SETLOCAL
PUSHD %~dp0
CD ..

SET "OPENSSL_CONF=%CD%\apache\bin\openssl.cnf"
SET "OPENSSL_CONF=%OPENSSL_CONF:\=/%"

IF NOT EXIST apache\conf\ssl.crt (
    MKDIR apache\conf\ssl.crt
)
IF NOT EXIST apache\conf\ssl.csr (
    MKDIR apache\conf\ssl.csr
)
IF NOT EXIST apache\conf\ssl.key (
    MKDIR apache\conf\ssl.key
)

apache\bin\openssl req -new -out server.csr
apache\bin\openssl rsa -in privkey.pem -out server.key
apache\bin\openssl x509 -in server.csr -out server.crt -req -signkey server.key -days 3650

DEL .rnd >nul 2>&1
DEL tmp\.rnd >nul 2>&1
DEL .oid >nul 2>&1
DEL tmp\.oid >nul 2>&1
DEL privkey.pem >nul 2>&1

MOVE /Y server.crt apache\conf\ssl.crt\
MOVE /Y server.csr apache\conf\ssl.csr\
MOVE /y server.key apache\conf\ssl.key\

ECHO.
ECHO -----
ECHO Das Zertifikat wurde erstellt.
ECHO The certificate was provided.
ECHO.

POPD
PAUSE
