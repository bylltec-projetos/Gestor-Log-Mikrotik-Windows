@ECHO OFF & SETLOCAL
PUSHD %~dp0

ECHO Now we stop Mercury
xampp_cli.exe stop mercury

POPD
PAUSE
