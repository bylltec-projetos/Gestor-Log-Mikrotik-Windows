@ECHO OFF & SETLOCAL
PUSHD %~dp0

IF EXIST webalizer.lck (
    ECHO Webalizer is currently running.
    POPD
    GOTO :EOF
) ELSE (
    ECHO. >webalizer.lck
)
    
ECHO resolving IP-addresses in Apache's access logfile...
..\apache\bin\logresolve.exe -s logresolve.stat < ..\apache\logs\access.log > logresolve-access.log
FINDSTR /R "^logresolve.Statistics: ^Entries: ^....With.name...: ^....Resolves....: ^....-.No.data...: ^....-.No.address: ^Cache.hits......: ^Cache.size......: ^Cache.buckets...:" logresolve.stat
ECHO.

ECHO generating statistic with Webalizer...
webalizer.exe -c webalizer.conf logresolve-access.log
ECHO.

DEL logresolve.stat >nul 2>&1
DEL logresolve-access.log >nul 2>&1
DEL webalizer.lck >nul 2>&1

POPD
