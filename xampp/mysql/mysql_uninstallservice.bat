@ECHO OFF & SETLOCAL
PUSHD %~dp0
CD ..

ECHO Time to say goodbye to MySQL :(
xampp_cli.exe deinstallservice mysql

POPD
PAUSE
