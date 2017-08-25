:: XAMPP Uninstaller
::
:: author     Carsten Wiedmann <carsten_sttgt@gmx.de>
:: author     Kay Vogelgesang <kvo@apachefriends.org>
:: copyright  2009 Carsten Wiedmann
:: license    http://www.freebsd.org/copyright/freebsd-license.html FreeBSD License
:: version    1.0
@ECHO OFF & SETLOCAL
CD %~dp0

MKDIR uninst.temp >nul 2>&1
COPY /Y install\xampp_uninstall.vbs uninst.temp >nul 2>&1

CSCRIPT.EXE //Nologo uninst.temp\xampp_uninstall.vbs %1
SET "_err=%ERRORLEVEL%"
IF %_err% NEQ 0 (
    ECHO.
    ECHO XAMPP uninstall not OK
    IF "%1" NEQ "auto" (
        ECHO.
        PAUSE
    )
    EXIT /B 1
)

DEL /F /Q uninst.temp\xampp_uninstall.vbs >nul 2>&1
RMDIR /Q uninst.temp >nul 2>&1

PING 127.0.0.1 -n 2 -w 1000 >nul 2>&1
PING 127.0.0.1 -n 2 -w 1000 >nul 2>&1

ECHO.
ECHO XAMPP uninstall OK
SET "_xamppdir=%CD%"
CD ..
START "" /MIN "%COMSPEC%" /C "DEL /F /Q "%_xamppdir%\uninstall_xampp.bat" & RMDIR /Q "%_xamppdir%""
