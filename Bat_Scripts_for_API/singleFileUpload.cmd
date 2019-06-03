@echo off
SET /p _string=Drag and Drop APK/IPA:
Set _string=%_string:"=%
curl -F "fileToUpload=@%_string%" -F "uploadOnly='checked'" http://localhost/gitAppinspector/apiCalls.php

:choice
set /P c=Do you want to upload a log [Y/N]?
if /I "%c%" EQU "Y" goto :upload_log
if /I "%c%" EQU "N" goto :finish
goto :choice

:upload_log

curl --silent --output nul localhost/gitAppinspector/logUploaded.php
echo Log Uploaded!
curl http://localhost/gitAppinspector/clearDirectory.php
pause
exit

:finish
curl http://localhost/gitAppinspector/clearDirectory.php
exit