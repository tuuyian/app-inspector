function uploadAPK()
{
    var displayInfo = document.getElementById('displayInfo');
    
    displayInfo.innerHTML = '<div id="form-container"><form action="upload.php" method="post" enctype="multipart/form-data"><div class="form-group"><input type="file" class="form-control px-3" name="fileToUpload" id="fileToUpload" accept=".apk, .ipa"></div><div class="form-group"><input type="checkbox" name="logoCheck" value="checked"> Logo <br><input type="checkbox" name="sslCheck" value="checked"> SSL Pinning <br><input type="checkbox" name="certificateCheck" value="checked"> Certificate <br><input type="checkbox" name="manifestCheck" value="checked"> Manifest <br></div><span class="input-group-btn"><button type="submit" name="submit" style="float: right;" class="btn btn-secondary btn-form display-4">Upload APK/IPA</button></span></form></div>';
}

function uploadIPA()
{
    var displayInfo = document.getElementById('displayInfo');
    
    displayInfo.innerHTML = '<div id="form-container"><form action="upload.php" method="post" enctype="multipart/form-data"><div class="form-group"><input type="file" class="form-control px-3" name="fileToUpload" id="fileToUpload" accept=".apk, .ipa"></div><div class="form-group"><input type="checkbox" name="infoCheck" value="checked"> Info.Plist <br><input type="checkbox" name="embeddedCheck" value="checked"> Embedded.mobileprovision <br></div><span class="input-group-btn"><button type="submit" name="submit" style="float: right;" class="btn btn-secondary btn-form display-4">Upload APK/IPA</button></span></form>';
}