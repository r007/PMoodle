@echo off
cls
COLOR B0
TITLE UNIFORM SERVER - Stop Server as program

rem ###################################################
rem # Name: Stop_and_Save.bat
rem # Created By: r007
rem # V 1.0 07-06-2012
rem ##################################################
echo.

rem --- working directory current folder 
pushd %~dp0

rem change to drive
w:

rem --- Change to UniServer root folder and save as variable 
cd ..\..\..\..
set ROOT=%cd%

rem --- Change working directory to Apache bin folder
cd %ROOT%\usr\local\apache2\bin

rem --- Get Apache file name. User may have ran multi-servers. Save as variable 
FOR /F "tokens=*" %%i in ('dir /B httpd*.exe') do SET BUS_APACHE_EXE=%%i

rem --- VBScript
:wscript.echo " Stopping Apache Server"
:Set objShell = CreateObject("WScript.Shell")         'Create shell object
:Set colEnvironment = objShell.Environment("PROCESS") 'Get list of environment variables
:US_APACHE_EXE = colEnvironment("BUS_APACHE_EXE")     'Get Apache exe name variable

:Set objWMIService = GetObject("winmgmts:\\.\root\cimv2") 'access CIM library
: Set procItem = objWMIService.ExecQuery("Select * from Win32_process") 'query

: For each objItem in procItem              'Iterate through all items returned
:  If objItem.Name = US_APACHE_EXE Then     'Check for named process
:    objItem.Terminate()                    'kill process using terminate function 
:  End If
: Next                                      'Process next item

:Set objShell       = Nothing               'Cleanup Objects
:Set colEnvironment = Nothing 
:Set objWMIService  = Nothing
:Set procItem       = Nothing
:wscript.echo " Apache Server Stopped"

rem --- pid exists delete 
SET APACHE_PID=%ROOT%\usr\local\apache2\logs\httpd.pid
if exist %APACHE_PID% (del %APACHE_PID%)

rem --- Change working directory to MySQL bin folder
cd %ROOT%\usr\local\mysql\bin

rem ### VBScript ###
: us_mysql_exe ="mysqld1.exe"                                       'Set default MySQL exe name
:'---Get MySQL name user may have ran multi-servers
: Set objFileScripting = CreateObject("Scripting.FileSystemObject") 'get file scripting object
: Set objFolder = objFileScripting.GetFolder(".\")                  'Return folder object
: Set filecollection = objFolder.Files                              'return file collection

: Set objRegEx = New RegExp                                         'Create new reg obj
: objRegEx.Pattern = "(mysqld\d+\.exe)"                             'Pattern to search for

: For Each filename in filecollection                               'filename = full path and name 
:   name=right(filename,len(filename)-InStrRev(filename, "\"))      'extract name only

:   'Perform regex 
:   Set colMatches = objRegEx.Execute(name)          'Return collection of Match objects 
:   If colMatches.Count > 0 Then                     'Match found
:     us_mysql_exe = colMatches(0).SubMatches(0)     'Extract first capturing group
:     Exit For                                       'Nothing else to do hence end
:   End If
: Next

:wscript.echo " Stopping MySQL Server"
:Set objWMIService = GetObject("winmgmts:\\.\root\cimv2") 'access CIM library
: Set procItem = objWMIService.ExecQuery("Select * from Win32_process") 'query

: For each objItem in procItem              'Iterate through all items returned
:  If objItem.Name = us_mysql_exe Then      'Check for named process
:    objItem.Terminate()                    'kill process using terminate function 
:  End If
: Next                                      'Process next item

:Set objShell       = Nothing               'Cleanup Objects
:Set colEnvironment = Nothing 
:Set objWMIService  = Nothing
:Set procItem       = Nothing
:wscript.echo " MySQL Server Stopped"

rem -- run above script
findstr "^:" "%~sf0">temp.vbs
cscript //nologo temp.vbs
del temp.vbs

echo.
rem --- restore original working directory
popd

subst W: /D
