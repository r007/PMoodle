###############################################################################
# Name: Read_me_first.txt
# Created By: The Uniform Server Development Team
# Edited Last By: Mike Gleaves (ric)
###############################################################################

 Uniform Server provides three scripts to generate a server certificate 
 and key pair.

   1) UniServer\openssl\Generate_server_cert_and_key.bat
   2) UniServer\openssl\Generate_server_cert_and_key.vbs
   3) UniServer\uni_con\scripts\Key_cert_gen.hta

 1) Generate_server_cert_and_key.bat
 
 This script generates a self-signed server certificate and key pair.
 It assumes you have not changed the server name from its default of localhost.
 This allows certificate and key to be automatically generated and installed
 without any user input.

 Note: The certificate signing request is not required hence is deleted.

 2) Generate_server_cert_and_key.vbs

 This script also generates a self-signed server certificate and key pair.
 It assumes you have changed the server name from its default of localhost.
 A popup displays the current server name setting. You can either accept this
 displayed value or change it as required. Pressing OK in either case generates
 and installs the certificate and key.

 Note: The certificate signing request is not required hence is deleted.

 3) Key_cert_gen.hta

 Self-signed certificate:

 This script is similar to the above in that it generates a self-signed server
 certificate and key pair. Several certificate defaults are displayed including
 server-name. For a self-signed certificate you need only change the
 server-name or accept its default. Pressing “Run Generate” generates and
 installs the certificate and key.

 Signed certificate:
 
 If you are intending to purchase a signed certificate fill in all appropriate
 form fields. Pressing “Run Generate” generates and installs the certificate
 (self-signed) and key.

 Unlike the above two key-cert generation scripts this script does not delete
 the certificate-signing request (server.csr). This is located in folder
 UniServer\openssl you open this file and post the contents for signing.

 Note 1: Copy both the server key
         UniServer\usr\local\apache2\server_certs\ssl.key\server.key and
         returned signed certificte to a USB memory stick for safekeeping.

 Note 2: For a free signed certificate check out the following page:
         UniServer\docs\English\apache_free_server_cert.html

                                  --- End ---









 
