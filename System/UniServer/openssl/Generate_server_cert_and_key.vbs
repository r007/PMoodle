'##############################################################################
'# Name: Generate_server_cert_and_key.vbs
'# Created By: The Uniform Server Development Team
'# Edited Last By: Mike Gleaves (ric)
'# V 1.0 27-6-2011
'# This script automatically generates a self-signed server certificate and key.
'# The main server name is automatically detected and displayed; user can change
'# this if required. Certificate protection, users may be using their own ca or
'# real signed certificates hence do not overwrite inform user accordingly.
'##############################################################################

Option Explicit                             '* Forces the explicit declaration 
us_set_working_directory                    '* Run sub sets current working dir
includeFile "..\uni_con\includes\core_config_inc.vbs"    '* Load core config
includeFile "..\uni_con\includes\core_functions_inc.vbs" '* Load core functions

'------------------------------------------------------------------------------
' Set working directory to script location
Sub us_set_working_directory
 Dim FSO, WshShell, path                                 '* Declare variables
 Set WshShell = CreateObject("WScript.Shell")            '* Create shell object
 Set FSO = CreateObject("Scripting.FileSystemObject")    '* Create file system object
 path = FSO.GetFile(Wscript.ScriptFullName).ParentFolder '* Get path to this file
 WshShell.CurrentDirectory = path                        '* Set new working directory
End Sub
'------------------------------------------------------------------------------

'==============================================================================
' This sub simulates PHP's include directive
sub includeFile (fSpec)
 dim objFSO,objFile,fileData
 Set objFSO  = CreateObject("Scripting.FileSystemObject")'* Create file obj
 Set objFile = objFSO.OpenTextFile(fSpec)                '* Open file for read
 fileData = objFile.readAll()                            '* Read file to string
 objFile.close                                           '* Close file
 executeGlobal fileData                                  '* Run code in string
 set objFSO  = nothing                                   '* Clean-up remove obj
 set objFile = nothing                                   '* Clean-up remove obj
end sub
'==============================================================================
 Dim objShell,str,cmd1,cmd2,cmd3,cmd4,str1,str2,str3,str4,str5,server_name,CNin
 Dim FS
 'Check for CA certificate
 If us_file_exists(USF_CERT_CA) Then
   str = ""
   str = str & "It looks like you are using your own CA." & vbCRLF & vbCRLF
   str = str & "To avoid overwriting your current server certificate and key" & vbCRLF
   str = str & "echo  this script has terminated." & vbCRLF  & vbCRLF
   str = str & "To create a new server certificate and key use the CA script." & vbCRLF
   MsgBox str,,"CA Found "
   WScript.Quit
 End If

 'Check for Server certificate
 If us_file_exists(USF_CERT) Then
   str = ""
   str = str & "A server certificate was found." & vbCRLF & vbCRLF
   str = str & "To avoid overwriting your current server certificate and key" & vbCRLF
   str = str & "this script has terminated." & vbCRLF & vbCRLF
   str = str & "To create a new server certificate and key use:" & vbCRLF
   str = str & "UniServer\uni_con\scripts\Key_cert_gen.hta" & vbCRLF

  MsgBox str,,"Certificate Found"
   WScript.Quit
 End If

 'Get server name from configuration file
 server_name = us_get_server_name()

 'Get user input
 CNin=InputBox("Enter common name e.g fred.com " & Chr(13) & Chr(13) & _
     "Or click OK for default","Common name required",server_name)

 'Set default to server name if user does not supply a common name
 If CNin ="" Then
  CNin=server_name
 End If

  cmd1 = " && set OPENSSL_CONF=C:\test_ssl\openssl.cnf " 

  str1 = "openssl req -newkey rsa:2048 -batch -nodes -out server.csr"
  str2 = " -keyout server.key -subj"
  str3 = " " & Chr(34) 
  str4 = "/C=US/ST=Cambs/L=Cambridge/O=UniServer/emailAddress=me@fred.com/CN=" & CNin
  str5 =  "" & Chr(34) 

  cmd2 = " && " & str1 & str2 & str3 &str4 &str5
  cmd3 = " && openssl x509 -in server.csr -out server.crt -req -signkey server.key -days 3650"
  cmd4 = " && set OPENSSL_CONF="

  Set objShell = CreateObject("WScript.Shell")
  objShell.Run "cmd /T:B0 /c  title US Test " & cmd1 & cmd2 & cmd3 & cmd4,0,true

  set FS=CreateObject("Scripting.FileSystemObject")

  'Create folder if not exist
  If Not FS.FolderExists(US_APACHE_CERTS) Then
   FS.CreateFolder(US_APACHE_CERTS)
  End If

  'Copy cert and key to folders
  FS.CopyFile US_OPENSSL & "\server.crt", US_APACHE_CERTS & "\",true
  FS.CopyFile US_OPENSSL & "\server.key", US_APACHE_CERTS & "\",true

  'Remove files
   If FS.FileExists(US_OPENSSL & "\server.crt") Then
    FS.DeleteFile US_OPENSSL & "\server.crt"
   End If

   If FS.FileExists(US_OPENSSL & "\server.key") Then
    FS.DeleteFile US_OPENSSL & "\server.key"
   End If

   If FS.FileExists(US_OPENSSL & "\server.csr") Then
    FS.DeleteFile US_OPENSSL & "\server.csr"
   End If

   'Enable SSL in configuration file
   us_enable_apache_ssl()

 'Inform user complete
   str = ""
   str = str & "Server certificate and key generated." & vbCRLF & vbCRLF
   str = str & "These have been installed and" & vbCRLF
   str = str & "SSL has been enabled in Apache's configuration file." & vbCRLF & vbCRLF
   str = str & "Restart servers for changes to take place." & vbCRLF

  MsgBox str,,"Certificate Generated"


