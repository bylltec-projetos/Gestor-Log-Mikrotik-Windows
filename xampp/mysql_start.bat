@ECHO OFF & SETLOCAL
PUSHD %~dp0

ECHO Now we start MySQL
xampp_cli.exe start mysql

POPD
PAUSE
