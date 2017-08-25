@ECHO OFF & SETLOCAL
PUSHD %~dp0

ECHO Now we start FileZilla
xampp_cli.exe start filezilla

POPD
PAUSE
