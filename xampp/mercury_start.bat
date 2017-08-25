@ECHO OFF & SETLOCAL
PUSHD %~dp0

ECHO Now we start Mercury
xampp_cli.exe start mercury

POPD
PAUSE
