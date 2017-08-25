@ECHO OFF & SETLOCAL
PUSHD %~dp0
CD ..

ECHO Installing FileZilla as a service
xampp_cli.exe installservice filezilla

IF NOT ERRORLEVEL 1 (
    ECHO Now we start FileZilla :)
    xampp_cli.exe startservice filezilla
)

POPD
PAUSE
