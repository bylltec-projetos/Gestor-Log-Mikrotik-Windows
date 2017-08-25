@ECHO OFF & SETLOCAL
PUSHD %~dp0
CD ..

ECHO Installing Mercury as a service
xampp_cli.exe installservice mercury

IF NOT ERRORLEVEL 1 (
    ECHO Now we start Mercury :)
    xampp_cli.exe startservice mercury
)

POPD
PAUSE
