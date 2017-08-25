@ECHO OFF & SETLOCAL
PUSHD %~dp0
CD ..

ECHO Time to say goodbye to Apache :(
xampp_cli.exe deinstallservice apache

POPD
PAUSE
