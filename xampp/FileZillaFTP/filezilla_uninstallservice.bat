@ECHO OFF & SETLOCAL
PUSHD %~dp0
CD ..

ECHO Time to say goodbye to filezilla :(
xampp_cli.exe deinstallservice filezilla

POPD
PAUSE
