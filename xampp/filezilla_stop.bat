@ECHO OFF & SETLOCAL
PUSHD %~dp0

ECHO Now we stop FileZilla
xampp_cli.exe stop filezilla

POPD
PAUSE
