@ECHO OFF & SETLOCAL
PUSHD %~dp0

ECHO Now we start Apache
xampp_cli.exe start apache

POPD
PAUSE
