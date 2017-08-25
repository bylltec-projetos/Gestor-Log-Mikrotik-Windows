@ECHO OFF & SETLOCAL
PUSHD %~dp0
CD ..

ECHO Installing MySQL as a service
xampp_cli.exe installservice mysql

IF NOT ERRORLEVEL 1 (
    ECHO Now we start MySQL :)
    xampp_cli.exe startservice mysql
)

POPD
PAUSE
