@ECHO OFF & SETLOCAL
PUSHD %~dp0
CD ..

ECHO Time to say goodbye to Mercury :(
xampp_cli.exe deinstallservice mercury

POPD
PAUSE
