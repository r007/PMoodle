'!c:/windows/system32/cscript -nologo
' -----------------------------------
' Sample CGI VBScript 
' -----------------------------------

Wscript.Echo "Content-type: text/html" & vbLF & vbLF
Wscript.Echo "<h1>VBScript CGI Test</h1>"

'-- List User Environment Variables
Set wshShell = CreateObject( "WScript.Shell" )
Set wshUserEnv = wshShell.Environment( "USER" )
For Each strItem In wshUserEnv
  WScript.Echo strItem & "<br />"
Next
Set wshUserEnv = Nothing
Set wshShell   = Nothing

WScript.Echo strItem & "<br />"

'-- List Process Environment Variables
Set wshShell = CreateObject( "WScript.Shell" )
Set wshUserEnv = wshShell.Environment( "Process" )
For Each strItem In wshUserEnv
  WScript.Echo strItem & "<br />"
Next
Set wshUserEnv = Nothing
Set wshShell   = Nothing

'-- List A Process Environment Variable
Set wshShell = CreateObject( "WScript.Shell" )
Set objEnv = wshShell.Environment( "Process" )
  WScript.Echo  objEnv("REQUEST_URI") & "<br />"
Set objEnv     = Nothing
Set wshShell   = Nothing


Wscript.Quit 0


