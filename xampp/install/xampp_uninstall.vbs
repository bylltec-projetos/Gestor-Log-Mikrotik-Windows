Option Explicit
' XAMPP Uninstaller
'
' author     Carsten Wiedmann <carsten_sttgt@gmx.de>
' author     Kay Vogelgesang <kvo@apachefriends.org>
' copyright  2009 Carsten Wiedmann
' license    http://www.freebsd.org/copyright/freebsd-license.html FreeBSD License
' version    1.0


Private objFSO, objWshShell, objWshShellEnv
Private strCommand, strInput, strXAMPPLocation, strUninstallTempLoc, strUninstallBatchFile, strMySQLDataLoc, strApacheHtdocsLoc, strTomcatWebappsLoc
Private boolApacheHtdocs, boolMySQLData, boolAutoUninstall, boolAddOnTomcat, boolTomcatWebapps

Set objWshShell = WScript.CreateObject("WScript.Shell")
Set objFSO = Createobject("Scripting.FileSystemObject")

Set objWshShellEnv = objWshShell.Environment("SYSTEM")
strCommand = objWshShellEnv("COMSPEC")
Set objWshShellEnv = Nothing

strXAMPPLocation = objFSO.GetParentFolderName(objFSO.GetParentFolderName(WScript.ScriptFullName))
strUninstallTempLoc = objFSO.BuildPath(strXAMPPLocation, "uninst.temp")
strUninstallBatchFile = objFSO.BuildPath(strXAMPPLocation, "uninstall_xampp.bat")
strMySQLDataLoc = objFSO.BuildPath(objFSO.BuildPath(strXAMPPLocation, "mysql"), "data")
strApacheHtdocsLoc = objFSO.BuildPath(strXAMPPLocation, "htdocs")
strTomcatWebappsLoc = objFSO.BuildPath(objFSO.BuildPath(strXAMPPLocation, "tomcat"), "webapps")
objWshShell.CurrentDirectory = strXAMPPLocation

If (WScript.Arguments.Unnamed.Count) Then
    If (LCase(WScript.Arguments.Unnamed.Item(0)) = "auto") Then
        boolAutoUninstall = True
    Else
        boolAutoUninstall = False
    End If
Else
    boolAutoUninstall = False
End If

If objFSO.FolderExists(objFSO.BuildPath(objFSO.BuildPath(strXAMPPLocation, "tomcat"), "webapps")) Then
    boolAddOnTomcat = True
Else
    boolAddOnTomcat = False
End If


Private Sub TestWScript()
    If "wscript.exe" = LCase(objFSO.GetFileName(WScript.FullName)) Then
        WScript.Echo "Please run this tool with ""Cscript.exe //Nologo xampp_uninstall.vbs""."

        Set objFSO = Nothing
        Set objWshShell = Nothing
        WScript.Quit(1)
    End If
End Sub

Private Sub TestLocation()
    If objFSO.GetParentFolderName(WScript.ScriptFullName) <> strUninstallTempLoc Then
        WScript.Echo "Please run this tool with the batchfile ""uninstall_xampp.bat""."

        Set objFSO = Nothing
        Set objWshShell = Nothing
        WScript.Quit(1)
    End If
End Sub

Private Sub PrintTitle()
    Dim intI

    For intI = 1 To 24
        WScript.Echo VbCrLf
    Next
    WScript.Echo "#######################################################################"
    WScript.Echo "# xampp_uninstall v1.0 by (c) 2009 Carsten Wiedmann (FreeBSD License) #"
    WScript.Echo "# Send bug reports to the author at <carsten_sttgt@gmx.de>.           #"
    WScript.Echo "#---------------------------------------------------------------------#"
    WScript.Echo "# This Script will uninstall your XAMPP installation.                 #"
    WScript.Echo "#######################################################################"
    WScript.Echo VbCrLf
End Sub

Private Sub GetNotDelete()
    If (boolAutoUninstall = True) Then
        boolApacheHtdocs = False
        boolMySQLData = False

        WScript.Echo "I will not delete """ & strApacheHtdocsLoc & """."
        WScript.Echo "I will not delete """ & strMySQLDataLoc & """."
        If boolAddOnTomcat = True Then
            WScript.Echo "I will not delete """ & strTomcatWebappsLoc & """."
        End If
        WScript.Echo VbCrLf
        Exit Sub
    End If

    Do
        WScript.StdOut.Write "                                                    n" & Chr(13) & "Should I delete your Apache htdocs directory? (y/n) "
        strInput = LCase(WScript.StdIn.ReadLine)
    Loop Until ((strInput = "y") Or (strInput = "") Or (strInput = "n"))
    If (strInput = "y") Then
        boolApacheHtdocs = True
    Else
        boolApacheHtdocs = False
    End If

    Do
        WScript.StdOut.Write "                                                 n" & Chr(13) & "Should I delete your MySQL data directory? (y/n) "
        strInput = LCase(WScript.StdIn.ReadLine)
    Loop Until ((strInput = "y") Or (strInput = "") Or (strInput = "n"))
    If (strInput = "y") Then
        boolMySQLData = True
    Else
        boolMySQLData = False
    End If

    If boolAddOnTomcat = True Then
        Do
            WScript.StdOut.Write "                                                     n" & Chr(13) & "Should I delete your Tomcat webapps directory? (y/n) "
            strInput = LCase(WScript.StdIn.ReadLine)
        Loop Until ((strInput = "y") Or (strInput = "") Or (strInput = "n"))
        If (strInput = "y") Then
            boolTomcatWebapps = True
        Else
            boolTomcatWebapps = False
        End If
    End If

    WScript.Echo VbCrLf
    If boolApacheHtdocs = False Then
        WScript.Echo "I will not delete """ & strApacheHtdocsLoc & """."
    End If
    If boolMySQLData = False Then
        WScript.Echo "I will not delete """ & strMySQLDataLoc & """."
    End If
    If boolAddOnTomcat = True Then
        If boolTomcatWebapps = False Then
            WScript.Echo "I will not delete """ & strTomcatWebappsLoc & """."
        End If
    End If
    WScript.Echo VbCrLf
End Sub

Private Function ServiceStatus(strServiceName)
    Dim colServiceList, colItems
    Dim objItem

    Set colServiceList = GetObject("winmgmts:{impersonationLevel=Impersonate,(debug)}!\\.\root\cimv2")
    Set colItems = colServiceList.ExecQuery("SELECT * FROM Win32_Service WHERE name = '" & strServiceName & "'", , 48)

    ServiceStatus = -1
    For Each objItem in colItems
        If objItem.State  = "Stopped" Then
            ServiceStatus = 1
        Else
            ServiceStatus = 0
        End If
    Next
    If ServiceStatus = -1 Then
        ServiceStatus = 2
    End If

    Set colItems = Nothing
    Set colServiceList = Nothing
End Function

Private Sub StopDeinstallServers()
    Dim intServiceStatus, intResult, intI
    Dim strXAMPPCliExec
    Dim arrServers(4, 1)

    arrServers(0, 0) = "apache"
    arrServers(0, 1) = "Apache2.2"
    arrServers(1, 0) = "mysql"
    arrServers(1, 1) = "MySQL"
    arrServers(2, 0) = "filezilla"
    arrServers(2, 1) = "FileZilla Server"
    arrServers(3, 0) = "mercury"
    arrServers(3, 1) = "Mercury"
    arrServers(4, 0) = "tomcat"
    arrServers(4, 1) = "Tomcat6"

    strXAMPPCliExec = objFSO.BuildPath(strXAMPPLocation, "xampp_cli.exe")

    WScript.Echo "Stopping and deinstalling the services, if necessary..."

    For intI = 0 To 4
        intServiceStatus = ServiceStatus(arrServers(intI, 1))
        If intServiceStatus < 2 Then
            If intServiceStatus = 0 Then
                intResult = objWshShell.Run("""" & strCommand & """ /C """"" & strXAMPPCliExec & """ stopservice " & arrServers(intI, 0) & """""", 0, True)
            End If
            intResult = objWshShell.Run("""" & strCommand & """ /C """"" & strXAMPPCliExec & """ deinstallservice " & arrServers(intI, 0) & """""", 0, True)
        End If
        intResult = objWshShell.Run("""" & strCommand & """ /C """"" & strXAMPPCliExec & """ stop " & arrServers(intI, 0) & """""", 0, True)
    Next
End Sub

Private Sub StopCP()
    Dim colProcessList, colItems
    Dim objItem

    WScript.Echo "Stopping the XAMPP Control Panel, if necessary..."

    Set colProcessList = GetObject("winmgmts:{impersonationLevel=Impersonate,(debug)}!\\.\root\cimv2")
    Set colItems = colProcessList.ExecQuery("SELECT * FROM Win32_Process WHERE name = 'xampp-control.exe'")

    For Each objItem in colItems
        objItem.Terminate()
    Next

    Set colItems = Nothing
    Set colProcessList = Nothing
End Sub

Private Sub BackupDataDirs()
    WScript.Echo "Moving data directories to a save location, if necessary..."

    If boolApacheHtdocs = False Then
        If (objFSO.FolderExists(strApacheHtdocsLoc)) Then
            objFSO.MoveFolder strApacheHtdocsLoc, strUninstallTempLoc & "\"
        End If
    End If
    If boolMySQLData = False Then
        If (objFSO.FolderExists(strMySQLDataLoc)) Then
            objFSO.MoveFolder strMySQLDataLoc, strUninstallTempLoc & "\"
        End If
    End If
    If boolAddOnTomcat = True Then
        If boolTomcatWebapps = False Then
            If (objFSO.FolderExists(strTomcatWebappsLoc)) Then
                objFSO.MoveFolder strTomcatWebappsLoc, strUninstallTempLoc & "\"
            End If
        End If
    End If
End Sub

Private Sub RestoreDataDirs()
    Dim strUninstallApacheTempLoc, strMySQLDataTempLoc, strTomcatWebappsTempLoc

    strUninstallApacheTempLoc = objFSO.BuildPath(strUninstallTempLoc, objFSO.GetFileName(strApacheHtdocsLoc))
    strMySQLDataTempLoc = objFSO.BuildPath(strUninstallTempLoc, objFSO.GetFileName(strMySQLDataLoc))
    strTomcatWebappsTempLoc = objFSO.BuildPath(strUninstallTempLoc, objFSO.GetFileName(strTomcatWebappsLoc))

    WScript.Echo "Moving data directories back, if necessary..."

    If boolApacheHtdocs = False Then
        If (objFSO.FolderExists(strUninstallApacheTempLoc)) Then
            If Not (objFSO.FolderExists(objFSO.GetParentFolderName(strApacheHtdocsLoc))) Then
                objFSO.CreateFolder objFSO.GetParentFolderName(strApacheHtdocsLoc)
            End If
            objFSO.MoveFolder strUninstallApacheTempLoc, strApacheHtdocsLoc
        End If
    End If
    If boolMySQLData = False Then
        If (objFSO.FolderExists(strMySQLDataTempLoc)) Then
            If Not (objFSO.FolderExists(objFSO.GetParentFolderName(strMySQLDataLoc))) Then
                objFSO.CreateFolder objFSO.GetParentFolderName(strMySQLDataLoc)
            End If
            objFSO.MoveFolder strMySQLDataTempLoc, strMySQLDataLoc
        End If
    End If
    If boolAddOnTomcat = True Then
        If boolTomcatWebapps = False Then
            If (objFSO.FolderExists(strTomcatWebappsTempLoc)) Then
                If Not (objFSO.FolderExists(objFSO.GetParentFolderName(strTomcatWebappsLoc))) Then
                    objFSO.CreateFolder objFSO.GetParentFolderName(strTomcatWebappsLoc)
                End If
                objFSO.MoveFolder strTomcatWebappsTempLoc, strTomcatWebappsLoc
            End If
        End If
    End If
End Sub

Private Sub DeleteIcons()
    Dim strDesktop, strStartmenu

    strDesktop = objFSO.BuildPath(objWshShell.SpecialFolders("Desktop"), "XAMPP Control Panel.lnk")
    strStartmenu = objFSO.BuildPath(objWshShell.SpecialFolders("Programs"), "XAMPP for Windows")

    WScript.Echo "Deleting startmenu/dektop icons..."

    If (objFSO.FileExists(strDesktop)) Then
        objFSO.DeleteFile strDesktop, True
    End If
    If (objFSO.FolderExists(strStartmenu)) Then
        objFSO.DeleteFolder strStartmenu, True
    End If
End Sub

Private Sub DeleteFiles()
    Dim colFoldersList, colItems
    Dim objItem

    Set colFoldersList = objFSO.GetFolder(strXAMPPLocation)
    Set colItems = colFoldersList.SubFolders

    WScript.Echo "Deleting files and folders..."

    For Each objItem in colItems
        If (objItem.name <> objFSO.GetFileName(strUninstallTempLoc)) Then
'            WScript.Echo objItem.name
            objFSO.DeleteFolder objItem.name, True
        End If
    Next

    Set colItems = colFoldersList.Files
    For Each objItem in colItems
        If (objItem.name <> objFSO.GetFileName(strUninstallBatchFile)) Then
'            WScript.Echo objItem.name
            objFSO.DeleteFile objItem.name, True
        End If
    Next

    Set colItems = Nothing
    Set colFoldersList = Nothing
End Sub

Sub AskSure()
    If (boolAutoUninstall = True) Then
        Exit Sub
    End If

    Wscript.Echo VbCrLf
    Wscript.Echo Chr(7)
    Wscript.Echo "Last change to stop the uninstall process!"
    Wscript.Echo "The services and programs are now stopped and removed."
    Wscript.Echo "Now I will start deleting files..."
    Wscript.Echo VbCrLf
    Do
        WScript.StdOut.Write "                         n" & Chr(13) & "Should I continue? (y/n) "
        strInput = LCase(WScript.StdIn.ReadLine)
    Loop Until ((strInput = "y") Or (strInput = "") Or (strInput = "n"))
    If ((strInput = "n") Or (strInput = "")) Then
        Set objWshShellEnv = Nothing
        Set objFSO = Nothing
        WScript.Quit(1)
    End If
    Wscript.Echo VbCrLf

End Sub

Sub Main()
    TestWScript
    TestLocation
    PrintTitle
    GetNotDelete
    StopDeinstallServers
    StopCP
    AskSure
    DeleteIcons
    BackupDataDirs
    DeleteFiles
    RestoreDataDirs
End Sub

Main
Set objWshShellEnv = Nothing
Set objFSO = Nothing
WScript.Quit(0)

'' SIG '' Begin signature block
'' SIG '' MIISgAYJKoZIhvcNAQcCoIIScTCCEm0CAQExCzAJBgUr
'' SIG '' DgMCGgUAMGcGCisGAQQBgjcCAQSgWTBXMDIGCisGAQQB
'' SIG '' gjcCAR4wJAIBAQQQTvApFpkntU2P5azhDxfrqwIBAAIB
'' SIG '' AAIBAAIBAAIBADAhMAkGBSsOAwIaBQAEFOuu8agyBX0G
'' SIG '' 1+o/IVA+nvamX+7YoIINezCCA6gwggKQoAMCAQICAwR6
'' SIG '' VTANBgkqhkiG9w0BAQUFADA+MQswCQYDVQQGEwJQTDEb
'' SIG '' MBkGA1UEChMSVW5pemV0byBTcC4geiBvLm8uMRIwEAYD
'' SIG '' VQQDEwlDZXJ0dW0gQ0EwHhcNMDkwMzAzMTI1ODE1WhcN
'' SIG '' MjQwMzAzMTI1ODE1WjCBgzELMAkGA1UEBhMCUEwxIjAg
'' SIG '' BgNVBAoTGVVuaXpldG8gVGVjaG5vbG9naWVzIFMuQS4x
'' SIG '' JzAlBgNVBAsTHkNlcnR1bSBDZXJ0aWZpY2F0aW9uIEF1
'' SIG '' dGhvcml0eTEnMCUGA1UEAxMeQ2VydHVtIFRpbWUtU3Rh
'' SIG '' bXBpbmcgQXV0aG9yaXR5MIIBIjANBgkqhkiG9w0BAQEF
'' SIG '' AAOCAQ8AMIIBCgKCAQEA3u2pB/R2wF6wC+c/oECJYSQj
'' SIG '' U4SgAvXTsTPsT2mAl6qdGCxmzzX4z1yN40cn5UOG6Wdm
'' SIG '' PHjW4+kEND3PBWQyKqb7ZavAsSHPd6d7YbZt1tJ/u81V
'' SIG '' 2AM9sZIV8dJCkF4KHADdfqLyTAVAEg0dG+MfegsP0Cgu
'' SIG '' 3EzsXrChz8NVdo/ke3f/6IUVq8jxFT2Nx7suvrw5PYOa
'' SIG '' p3MQ/wMozx13kB57NZm3TAEfGjbBuJmDUlRm19YaUhKf
'' SIG '' RyyZKYVWxvRzwJ+s2XiHfegexpVApnnSuKTBFqtqmAVx
'' SIG '' SmPtyphsXUWXUpX0ZibIc++l3AnP66R7S/Sf6hsPVRx0
'' SIG '' rbHGDrPqzQIDAQABo2kwZzAWBgNVHSUBAf8EDDAKBggr
'' SIG '' BgEFBQcDCDAsBgNVHR8EJTAjMCGgH6AdhhtodHRwOi8v
'' SIG '' Y3JsLmNlcnR1bS5wbC9jYS5jcmwwHwYDVR0RBBgwFoYU
'' SIG '' aHR0cDovL3RzYS5jZXJ0dW0ucGwwDQYJKoZIhvcNAQEF
'' SIG '' BQADggEBAKqLG6LshUXrOIsKTXjPeIlTENpXWlsHWycM
'' SIG '' ydm5xAoqZ6y/B6s1wbQOb3lMe78Tv/p21W6uzaEUmV/y
'' SIG '' BIEUV5EE54uTRa6H8rnjWuh6NZF8OlYOWbfHDaY1G82c
'' SIG '' 0OZVOv4bOUjHX5ohlv0csnNSxP7xY7NSr+Qk5btnkGdC
'' SIG '' RbZ2rhPnIrcHy5ZGAei+PQ0N5yB+RkATiZYvVMo0UxMn
'' SIG '' f+zvZsSxCPcyIsIUqX9W+THu1C+teSE9ETP3067oy7xb
'' SIG '' zxb2i2hPDZz0bLgoWONIlpXUJJJXlHA8a9o66M6b0jor
'' SIG '' E+D9ggBXfw3cVtCpRbzZK5IXpxZtJW/zZz2nvudgnyow
'' SIG '' ggQ0MIIDHKADAgECAgMEelEwDQYJKoZIhvcNAQEFBQAw
'' SIG '' PjELMAkGA1UEBhMCUEwxGzAZBgNVBAoTElVuaXpldG8g
'' SIG '' U3AuIHogby5vLjESMBAGA1UEAxMJQ2VydHVtIENBMB4X
'' SIG '' DTA5MDMwMzEyNTIzMFoXDTI0MDMwMzEyNTIzMFowdjEL
'' SIG '' MAkGA1UEBhMCUEwxIjAgBgNVBAoTGVVuaXpldG8gVGVj
'' SIG '' aG5vbG9naWVzIFMuQS4xJzAlBgNVBAsTHkNlcnR1bSBD
'' SIG '' ZXJ0aWZpY2F0aW9uIEF1dGhvcml0eTEaMBgGA1UEAxMR
'' SIG '' Q2VydHVtIExldmVsIEkgQ0EwggEiMA0GCSqGSIb3DQEB
'' SIG '' AQUAA4IBDwAwggEKAoIBAQCmlPnwQjgDsTP7HswgasRD
'' SIG '' R/icIB7TOrWV3XKrWjAWoFUqEorZuLQkAKEONfzduKLp
'' SIG '' sF6JHUXo9mao7Xycybp3Ir+TAqrL8HWOU+dBflbrvQqh
'' SIG '' SSv234mPcnOqT0xTVGC/XsGoDZjzYepJJrxNheK0M5LE
'' SIG '' wRDA9+Ykcutkg7SBqNJE6a2CfOzBT8f5SKJqlDrivqaj
'' SIG '' SkbajbCuoy28nmFYe3RGlvao2toGNP/mfiluf4sdtTcD
'' SIG '' HT39SNsoSmk88bq2+H+SK/Wk7m6ANHME84zx4UVztRS9
'' SIG '' 7y3RorDhxznbcHb0NymBs/2mYLeRIndhhSq4qelU1KeO
'' SIG '' uZTM2nd/j251AgMBAAGjggEBMIH+MA8GA1UdEwEB/wQF
'' SIG '' MAMBAf8wDgYDVR0PAQH/BAQDAgEGMB0GA1UdDgQWBBTG
'' SIG '' Bb1jHeLYbig2H0XuNTzJf3LKOzBSBgNVHSMESzBJoUKk
'' SIG '' QDA+MQswCQYDVQQGEwJQTDEbMBkGA1UEChMSVW5pemV0
'' SIG '' byBTcC4geiBvLm8uMRIwEAYDVQQDEwlDZXJ0dW0gQ0GC
'' SIG '' AwEAIDAsBgNVHR8EJTAjMCGgH6AdhhtodHRwOi8vY3Js
'' SIG '' LmNlcnR1bS5wbC9jYS5jcmwwOgYDVR0gBDMwMTAvBgRV
'' SIG '' HSAAMCcwJQYIKwYBBQUHAgEWGWh0dHBzOi8vd3d3LmNl
'' SIG '' cnR1bS5wbC9DUFMwDQYJKoZIhvcNAQEFBQADggEBAGJG
'' SIG '' 1yFw8lU9ehTbpC/rF2t3qdaTC6UgdaWtHUteNo1UkDDu
'' SIG '' 6oaX5/dS30C2guDxpsPEjmPZMqpQ1OdCpfSzYx6A4UKV
'' SIG '' WdskybVleE9Zq+xszQSvQLRGKG4nNbIftsROPUA3FtFr
'' SIG '' SkjTiwrziJeP8vD4j/K0YrbTkh1abbslObQ/Ig6phE9A
'' SIG '' yhF3GH3mm8T+EVNaDyKbs+WZHRXpO53BKAwswPQdwRLN
'' SIG '' hxpwf+7r5gBHoqtWYfq8jUJ5BGtjEnekMWAnCzl7s2ku
'' SIG '' YXkBgj6cN/EvkQO6g1NjCnqtoFcW7Cii6hnbCZ6JtkUZ
'' SIG '' T1Ux5xqM/adrRxLZx8kNRUUh5Tju1p0wggWTMIIEe6AD
'' SIG '' AgECAgMGzmQwDQYJKoZIhvcNAQEFBQAwdjELMAkGA1UE
'' SIG '' BhMCUEwxIjAgBgNVBAoTGVVuaXpldG8gVGVjaG5vbG9n
'' SIG '' aWVzIFMuQS4xJzAlBgNVBAsTHkNlcnR1bSBDZXJ0aWZp
'' SIG '' Y2F0aW9uIEF1dGhvcml0eTEaMBgGA1UEAxMRQ2VydHVt
'' SIG '' IExldmVsIEkgQ0EwHhcNMDkxMDMxMjMzMzA2WhcNMTAw
'' SIG '' MTI5MjMzMzA2WjBtMQswCQYDVQQGEwJERTEeMBwGA1UE
'' SIG '' ChMVb3BlbiBzb3VyY2UgZGV2ZWxvcGVyMRkwFwYDVQQD
'' SIG '' ExBDYXJzdGVuIFdpZWRtYW5uMSMwIQYJKoZIhvcNAQkB
'' SIG '' FhRjYXJzdGVuX3N0dGd0QGdteC5kZTCCASIwDQYJKoZI
'' SIG '' hvcNAQEBBQADggEPADCCAQoCggEBALXCBfj6KMSkDMro
'' SIG '' Qw7F0O7+gZqrfNejK/U8O0+CcjrqmqBaI0GBdmj2sG9O
'' SIG '' h746asZ2FMFq6zU+INXNKi6S6+AXM0ZFchOa/a0bJ8Hr
'' SIG '' 5/KEsoG1MiWd1hTqfS581bb7mFOYcqLpgjySIMiaWnIr
'' SIG '' sK5If0Pcgo2iaMq507H+KEsIVfAXUsqufKm54Fx5zKIC
'' SIG '' X3tsKC54WseCb+DUQHTMsnpP9CXXMMbU1k4QcK2553QO
'' SIG '' BJY2SX19WfA1kaRRPPSv6K+QgeyiPHda5Eg4Dghk+p0R
'' SIG '' nBKtSjox/4R5tBnoD6qNaDWgIqi5IU821XEhEos4SKnN
'' SIG '' i8LlHEXEeeCTK44YZUECAwEAAaOCAjEwggItMAsGA1Ud
'' SIG '' DwQEAwIHgDATBgNVHSUEDDAKBggrBgEFBQcDAzAdBgNV
'' SIG '' HQ4EFgQUr1crWnjsQtyaTwM4rWvJmVO1n9wwHwYDVR0j
'' SIG '' BBgwFoAUxgW9Yx3i2G4oNh9F7jU8yX9yyjswLAYDVR0f
'' SIG '' BCUwIzAhoB+gHYYbaHR0cDovL2NybC5jZXJ0dW0ucGwv
'' SIG '' bDEuY3JsMFoGCCsGAQUFBwEBBE4wTDAhBggrBgEFBQcw
'' SIG '' AYYVaHR0cDovL29jc3AuY2VydHVtLnBsMCcGCCsGAQUF
'' SIG '' BzAChhtodHRwOi8vd3d3LmNlcnR1bS5wbC9sMS5jZXIw
'' SIG '' ggE9BgNVHSAEggE0MIIBMDCCASwGCiqEaAGG9ncCAgEw
'' SIG '' ggEcMCUGCCsGAQUFBwIBFhlodHRwczovL3d3dy5jZXJ0
'' SIG '' dW0ucGwvQ1BTMIHyBggrBgEFBQcCAjCB5TAgFhlVbml6
'' SIG '' ZXRvIFRlY2hub2xvZ2llcyBTLkEuMAMCAQEagcBVc2Fn
'' SIG '' ZSBvZiB0aGlzIGNlcnRpZmljYXRlIGlzIHN0cmljdGx5
'' SIG '' IHN1YmplY3RlZCB0byB0aGUgQ0VSVFVNIENlcnRpZmlj
'' SIG '' YXRpb24KUHJhY3RpY2UgU3RhdGVtZW50IChDUFMpIGlu
'' SIG '' Y29ycG9yYXRlZCBieSByZWZlcmVuY2UgaGVyZWluIGFu
'' SIG '' ZCBpbiB0aGUgcmVwb3NpdG9yeQphdCBodHRwczovL3d3
'' SIG '' dy5jZXJ0dW0ucGwvcmVwb3NpdG9yeS4wDQYJKoZIhvcN
'' SIG '' AQEFBQADggEBAKTagFSDhZKpC1GL55WGU7F02WI3DUIj
'' SIG '' 5X8GUzRl3zZ12HrpZnUdc/LKF/VHy6n7f3bMr3Ku9Q0/
'' SIG '' H5JeZR8F+wT70Pxf8o+rblo274WOxPqwrG9uyarLYAND
'' SIG '' lBQCt8Kpaydor2Agt6hcoQIV2soo+s1iaX5OWrhDvRUX
'' SIG '' DZkq0nsPwroSL5GLg4yN/ZUrRmQr5+nUHSyFKcExOAv6
'' SIG '' b28BtJcXUUNsRHYAN6SN6d6025h999E1vgj3ytry8+kG
'' SIG '' DeejqljYf4ZfH5+v0hoL+SbfZc8sNmOGi38N9aZKU62j
'' SIG '' 4QBLUXyLRI/Kb6rEVJsVYA7Zxr0asHhRuBbXOpHJoRlW
'' SIG '' +YUxggRxMIIEbQIBATB9MHYxCzAJBgNVBAYTAlBMMSIw
'' SIG '' IAYDVQQKExlVbml6ZXRvIFRlY2hub2xvZ2llcyBTLkEu
'' SIG '' MScwJQYDVQQLEx5DZXJ0dW0gQ2VydGlmaWNhdGlvbiBB
'' SIG '' dXRob3JpdHkxGjAYBgNVBAMTEUNlcnR1bSBMZXZlbCBJ
'' SIG '' IENBAgMGzmQwCQYFKw4DAhoFAKBwMBAGCisGAQQBgjcC
'' SIG '' AQwxAjAAMBkGCSqGSIb3DQEJAzEMBgorBgEEAYI3AgEE
'' SIG '' MBwGCisGAQQBgjcCAQsxDjAMBgorBgEEAYI3AgEVMCMG
'' SIG '' CSqGSIb3DQEJBDEWBBQE/4qR20XWziBMFzIrRsIPTziD
'' SIG '' XDANBgkqhkiG9w0BAQEFAASCAQBUN4C8ocxY4aNAL5jv
'' SIG '' 5V8aUSMg1jv96J/6lqOjbmtOprfPhwz7mPcpKNp08f6+
'' SIG '' cviptElBtiIhNtywPTVFx3psAzH7Beqv9Z/bFXn1rLCo
'' SIG '' nYrwNCgGcQwXFMlclyrkbRZoIB17FODskTDUUMu+vmfR
'' SIG '' I4WL9rNZdNDecNyOBY7dTXZM69gMXxkQkXduEniW8cte
'' SIG '' 9EO3tGdqkLTme398Ap/TzlTzMNCGF4XZ0kI3JOfPaMfA
'' SIG '' nFvwv7SBR2IV9xmbXTqF2LBeQTDkatQKQxN/pIXUcQzh
'' SIG '' 0a+lJYr7MLm6W3Iho++uNAIjqHI9cHjveDiCorhcNApj
'' SIG '' D2S7rHGpq/ZIKHsNoYICVzCCAlMGCSqGSIb3DQEJBjGC
'' SIG '' AkQwggJAAgEBMEUwPjELMAkGA1UEBhMCUEwxGzAZBgNV
'' SIG '' BAoTElVuaXpldG8gU3AuIHogby5vLjESMBAGA1UEAxMJ
'' SIG '' Q2VydHVtIENBAgMEelUwCQYFKw4DAhoFAKCB1TAYBgkq
'' SIG '' hkiG9w0BCQMxCwYJKoZIhvcNAQcBMBwGCSqGSIb3DQEJ
'' SIG '' BTEPFw0wOTExMTEwMDE5MzdaMCMGCSqGSIb3DQEJBDEW
'' SIG '' BBTln4agZKvxfgQsSYQ0es0513mfFzB2BgsqhkiG9w0B
'' SIG '' CRACDDFnMGUwYzBhBBQNLPli+00ELy8UAd5m6suoDadh
'' SIG '' EjBJMEKkQDA+MQswCQYDVQQGEwJQTDEbMBkGA1UEChMS
'' SIG '' VW5pemV0byBTcC4geiBvLm8uMRIwEAYDVQQDEwlDZXJ0
'' SIG '' dW0gQ0ECAwR6VTANBgkqhkiG9w0BAQEFAASCAQCp3dSQ
'' SIG '' QzFZyH1Yroz6mePp6m68F/3sijBqx5WCS/z4dDoEBZNE
'' SIG '' /09BuodIJjPmmizLxt43TFhaVjBxUyZdu52s4A6wDjq4
'' SIG '' Q7YZLl6mJmENXi0JdGUmdjEfbwIOFtA1hnugLKGf9mGY
'' SIG '' ahx9Crz5DHe6/7OEzB5crvdbcwYRZyCT5esnoUoxaTH1
'' SIG '' 0QYjoVYTxXG3tj6ojymcwerwvPRN6yjb6vWD+fTZqptl
'' SIG '' wR/K6JjQNrZH2Rw6/3wHtVRO6b4qITa6+T5e5kCKwMOb
'' SIG '' RCSOp6j4e3TmsOGfWLpv3J147FruUQ5kutWFfHTG7QN4
'' SIG '' wvE3RSOiV8YeSkFyV2lwFjuvSyxq
'' SIG '' End signature block
