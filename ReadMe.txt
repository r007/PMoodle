                                           CRITICAL BATCH FILES IN ROOT DIRECTORY !!!                                           
                                           
                                           



START.BAT- Starts WAMP server stack which starts Moodle. THIS FILE STARTS EVERYTHING RELATED TO PORTABLE MOODLE !!! If your not sure 
check task manager look for Apache processes(2) and mysql process.  
 Launches to Start Page then you click link 
to get into Moodle. IF AUTO START DOESN'T WORK DOUBLE CLICK START.BAT !


Note for Vista Users !!!


Because of Vista's "advanced" security it causes problems for Portable Moodle. These issues are being worked on.
To start Portable Moodle on Vista you must use \System\Server_start.bat and rt click and "run as" Administrator. 
Right now there is limited function with Vista. 



STOPPING THINGS 

If you close Firefox Portable browser it doesn't close Moodle. The only way to close Moodle is with the PStart Menu. 
If you click on the X on the PStart menu that doesn't work either it is still running on the tray at the bottom of 
the screen. YOU MUST choose either STOP SAVE or STOP DELETE. STOP DELETE will make sure it deletes your stuff 
thoroughly if you do this you CANNOT RETRIEVE your files. Make sure you know that there's nothing that you've added
 to Moodle that you want to keep. It will not erase Moodle or whatever came on the CD but anything you've added 
since will be gone. 



Mode Notes

The pm1.20en_mini version installs from the PStart menu and by default in R/W mode and is approx 135mb. To restore 
Moodle courses or create Moodle courses or change Moodle you must be in this mode. Then if you decide you want to burn 
a CD you must switch to CD mode by running System\mode_cd.bat and then burn your CD. Do NOT RUN MOODLE IN CD MODE 
FROM YOUR HARD DRIVE UNLESS YOU HAVE READ ALL THE TECH DOCUMENTATION. RUNNING SYSTEM\MODE_CD.BAT IS THE LAST THING YOU DO
BEFORE YOU BURN YOUR CD. TO MAKE ANY CHANGES TO MOODLE YOU MUST BE IN R/W MODE. 


The pm1.20en full install is for Moodle course distrubutors. This version is what you downloaded from the web. It 
contains all the tools necessary to develope Portable Moodle. It is approximately 500 mb. You can distribute 
moodle courses with this version as well but is much bigger and you won't be able to put much large media content
on 650 mb CD or 1 GB USB stick. It is recommended you use pm1.20en_mini to deliver to students. pj