@ECHO OFF & SETLOCAL
PUSHD %~dp0

ECHO Now we stop Apache
xampp_cli.exe stop apache

POPD
PAUSE
