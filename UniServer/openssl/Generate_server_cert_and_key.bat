@echo off
cls
COLOR B0
mode con:cols=80 lines=20
TITLE UNIFORM SERVER - Server Certificate and Key gen

rem ###################################################
rem # Name: Generate_server_cert_and_key.bat
rem # Created By: The Uniform Server Development Team
rem # Edited Last By: Mike Gleaves (ric)
rem # V 1.0 27-6-2011
rem # This script automatically generates a self-signed 
rem # server certificate and key for localhost with no
rem # user intervention. A user may be using their own
rem # ca or real signed certificates hence do not
rem # overwrite.
rem ##################################################

rem ### working directory current folder 
pushd %~dp0

rem ### Check for ca or certificate exit if found
if exist ..\usr\local\apache2\server_certs\ca.crt goto CA_FOUND
if exist ..\usr\local\apache2\server_certs\server.crt goto CRT_EXIST

rem ### If folder does not exist create it
if not exist ..\usr\local\apache2\server_certs mkdir ..\usr\local\apache2\server_certs

rem ### Generate certificate and key
set OPENSSL_CONF=.\openssl.cnf
openssl req -newkey rsa:2048 -batch -nodes -out server.csr -keyout server.key -subj "/C=US/ST=Cambs/L=Cambridge/O=UniServer/emailAddress=me@fred.com/CN=localhost"
openssl x509 -in server.csr -out server.crt -req -signkey server.key -days 3650
set OPENSSL_CONF=

rem ### Delete certificate signing request and move certificate and key to server
del server.csr
move /y server.crt ..\usr\local\apache2\server_certs
move /y server.key ..\usr\local\apache2\server_certs

cls
echo.
echo  === Created ===
echo.
echo  Certificate and Key created and copied to server.
echo.
echo  === Enable ssl
echo.
echo  Edit file:        UniServer\usr\local\apache2\conf\httpd.conf
echo.
echo  Change this line: #LoadModule ssl_module modules/mod_ssl.so
echo  To:               LoadModule ssl_module modules/mod_ssl.so
echo.
echo  For the changes to take please restart Apache server.
echo. 
pause
goto END

:CA_FOUND
echo.
echo  === CA Found ===
echo.
echo  It looks like you are using your own CA.
echo.
echo  To avoid overwriting your current server certificate and key
echo  this script has terminated.
echo.
echo  To create a new server certificate and key use the CA script.
echo.
pause
goto END

:CRT_EXIST
echo.
echo  === Certificate Found ===
echo.
echo  A server certificate was found.
echo.
echo  To avoid overwriting your current server certificate and key
echo  this script has terminated.
echo.
echo  To create a new server certificate and key use:
echo  UniServer\uni_con\scripts\Key_cert_gen.hta
echo.
pause
goto END

:END
popd
exit

