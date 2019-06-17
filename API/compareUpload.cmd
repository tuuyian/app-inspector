@echo off
SET /p _string=Drag and Drop APK/IPA:
Set _string=%_string:"=%
:choice
set /P c=Do you want to compare to another file [Y/N]?
if /I "%c%" EQU "Y" goto :compare_file
if /I "%c%" EQU "N" goto :compare_log
goto :choice


:compare_file

SET /p _string1=Drag and Drop APK/IPA:
Set _string1=%_string1:"=%

curl -F "fileToUpload=@%_string%" -F "fileToUpload1=@%_string1%" -F "compareTwoFiles='checked'" -F "CMD='checked'" http://localhost/app-inspector-Final/apiCalls.php
pause
curl http://localhost/app-inspector-Final/clearDirectory.php
exit

:compare_log

curl -F "fileToUpload=@%_string%" -F "compareWithLog='checked'" -F "CMD='checked'" http://localhost/app-inspector-Final/apiCalls.php
pause
curl http://localhost/app-inspector-Final/clearDirectory.php
exit