@ECHO OFF & SETLOCAL
PUSHD %~dp0

ECHO Now we stop MySQL
xampp_cli.exe stop mysql

POPD
PAUSE
