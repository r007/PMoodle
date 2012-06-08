cls
@echo off
COLOR B0
TITLE UNIFORM SERVER - Start Server as program

rem ###################################################
rem # Name: Server_Start.bat
rem # Created By: r007
rem # V 1.0 07-06-2012
rem ##################################################
echo.

rem --- working directory current folder 
pushd %~dp0

start PStart.exe

rem map drive letter to current directory
subst W: "%CD%\UniServer"

rem change to drive
w:

rem ###############################################
rem ### Uniform Server - Apache start         
rem ###############################################

rem --- Change to UniServer root folder and save as variable 
cd ..\..\..\..
set ROOT=%cd%
set ROOT_APACHE=%ROOT%
set ROOT_MYSQL=%ROOT%

rem --- Change working directory to Apache bin folder
cd %ROOT%\usr\local\apache2\bin

rem --- Get Apache file name. User may have ran multi-servers. Save as variable 
FOR /F "tokens=*" %%i in ('dir /B httpd*.exe') do SET BUS_APACHE_EXE=%%i

rem --- If pid file exists it is an artifact hence delete
SET APACHE_PID=%ROOT%\usr\local\apache2\logs\httpd.pid
if exist %APACHE_PID% (del %APACHE_PID%)

rem ### VBScript ###
:'--- Get environment variables
:Set objShell = CreateObject("WScript.Shell")        'Create shell object
:Set colEnvironment = objShell.Environment("PROCESS")'Get list of environment variables
:US_APACHE_EXE = colEnvironment("BUS_APACHE_EXE")    'Get Apache exe name variable
:US_ROOT       = colEnvironment("ROOT_APACHE")              'Get Root path variable
:US_APACHE     = US_ROOT &"\usr\local\apache2"       'Set Apache path variable
:US_APACHE_BIN = US_ROOT &"\usr\local\apache2\bin"   'Set Apache bin path variable

:'--- Check Apache running 
: Set objWMIService = GetObject("winmgmts:\\.\root\cimv2") 'access CIM library
: Set procItem = objWMIService.ExecQuery("Select * from Win32_process") 'query

: For each objItem in procItem              'Iterate through all items returned
:  if objItem.Name = US_APACHE_EXE Then     'Check for named process
:    wscript.echo " Apache alreay running!" 'Named process found inform user  
:    Wscript.Quit 0                         'Nothing else to do exit
:  End if
: Next                                      'Process next item

:'--- Start Apache 
:wscript.echo " Starting Apache Server"             'Inform user
:strCmd1 = US_APACHE_BIN & "\" & US_APACHE_EXE      'Absolute path to exe 
:strCmd2 = " -f " & US_APACHE & "\conf\httpd.conf " 'Absolute path to config 
:strCmd3 = " -d " & US_APACHE                       'Set apache base path
:strCmd = strCmd1 & strCmd2 & strCmd3               'Assemble complete command
:objShell.Run strCmd, 0                             'Run process detached hidden
:wscript.echo " Apache Server started"              'Inform user

:Set objShell      = Nothing                        'Cleanup Object
:Set objWMIService = Nothing 
:Set procItem      = Nothing 
:Set objWMIService = Nothing 
rem ### END VBScript ###

rem ###############################################
rem ### Uniform Server - MySQL start         
rem ###############################################

rem --- Change working directory to MySQL bin folder
cd %ROOT%\usr\local\mysql\bin

rem ### END VBScript ###
: us_mysql_exe ="mysqld1.exe"                           'Set default MySQL exe name

:'--- Get environment variables
:Set objShell = CreateObject("WScript.Shell")           'Create shell object
:Set colEnvironment = objShell.Environment("PROCESS")   'Get list of environment variables
:US_ROOT       = colEnvironment("ROOT_MYSQL")                 'Get Root path variable
:US_MYSQL      = US_ROOT &"\usr\local\mysql"            'Set MySQL path variable
:US_MYSQL_BIN  = US_ROOT &"\usr\local\mysql\bin"        'Set MySQL bin path variable

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

:'--- Check MySQL running 
: Set objWMIService = GetObject("winmgmts:\\.\root\cimv2") 'access CIM library
: Set procItem = objWMIService.ExecQuery("Select * from Win32_process") 'query
: For each objItem in procItem              'Iterate through all items returned
:  if objItem.Name = us_mysql_exe Then      'Check for named process
:    wscript.echo " MySQL alreay running!"  'Named process found inform user  
:    Wscript.Quit 0                         'Nothing else to do exit
:  End if
: Next                                      'Process next item

:'--- Start MySQL Server
:wscript.echo " Starting MySQL Server"               'Inform user
:strCmd1 = US_MYSQL_BIN & "\" & us_mysql_exe         'Absolute path to exe 
:                                                    'Absolute path to config 
:strCmd2 = " --defaults-file="& Chr(34) & US_MYSQL & "\my.ini" &  Chr(34)  
:strCmd = strCmd1 & strCmd2                          'Assemble complete command
:Set WshShell = CreateObject("WScript.Shell")        'Create a shell object
:WshShell.Run strCmd, 0                              'Run process detached hidden
:wscript.echo " MySQL Server started"                'Inform user

rem ### END VBScript ###

rem -- run above script
findstr "^:" "%~sf0">temp.vbs
cscript //nologo temp.vbs
del temp.vbs

echo.
rem --- restore original working directory
popd

start %ROOT%FirefoxPortable\FirefoxPortable.exe http://localhost:4001/RU/index.shtml

