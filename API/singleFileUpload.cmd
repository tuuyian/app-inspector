@echo off
SET /p _string=Drag and Drop APK/IPA:
Set _string=%_string:"=%
curl -F "fileToUpload=@%_string%" -F "uploadOnly='checked'" http://localhost/app-inspector-Final/apiCalls.php

:choice
set /P c=Do you want to upload a log [Y/N]?
if /I "%c%" EQU "Y" goto :upload_log
if /I "%c%" EQU "N" goto :finish
goto :choice

:upload_log

curl --silent --output nul localhost/app-inspector-Final/logUploaded.php
echo Log Uploaded!
curl http://localhost/app-inspector-Final/clearDirectory.php
pause
exit

:finish
curl http://localhost/app-inspector-Final/clearDirectory.php
exit