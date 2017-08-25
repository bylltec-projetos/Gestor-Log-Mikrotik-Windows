@ECHO OFF & SETLOCAL
PUSHD %~dp0
CD ..

ECHO Installing Apache as a service
xampp_cli.exe installservice apache

IF NOT ERRORLEVEL 1 (
    ECHO Now we start Apache :)
    xampp_cli.exe startservice apache
)

POPD
PAUSE
