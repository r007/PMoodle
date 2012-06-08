' Name: Start MS-DOS File 
' Created By: The Uniform Server Development Team
' Edited Last By: Olajide Olaolorun (empirex)
' Comment: Re-Arranged everything to look nicely. 
' To Developers: Added the MySQL start option. 

Dim WSHShell, dir, fso, f1 
Set fso = CreateObject("Scripting.FileSystemObject") 
Set WSHShell = WScript.CreateObject("WScript.Shell") 

' Uncoment next line to ask about virtual disk for server (useful for CD or USB stick distributions) 
s=InputBox("Specify a Disk Drive for the Server to use... (one character please)","Server Disk","W") 

If intDoIt = vbCancel Then 
     WScript.Quit 
End If 
s=mid(s,1,1) 

t=MsgBox("Start the MySQL Database Server?", vbYesNo + vbQuestion, "Database Support") 
If intDoIt = vbNo Then 
    m="" 
Else 
    m=" mysql" 
End If 

WSHShell.run "Server_Start.bat "&s&m,0,0 
WScript.sleep(1000) 

' --- If you do not want to open default browser at server start - Not needed, Commented
'WSHShell.run "udrive\home\admin\www\redirect.html"