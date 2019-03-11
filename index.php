<!DOCTYPE html>
<html>
<body>

<form action="upload.php" method="post" enctype="multipart/form-data">
    Select image to upload:
    <input type="file" name="fileToUpload" id="fileToUpload" accept=".apk, .ipa">
    <input type="submit" value="Upload APK/IPA" name="submit">
</form>
<?php

    if(count(glob("uploads/*"))!=0) 
    {
        $src = 'uploads';
        $dir = opendir($src);
        while(false !== ( $file = readdir($dir)) ) 
        {
            if (( $file != '.' ) && ( $file != '..' )) 
            {
                $full = $src . '/' . $file;
                if ( is_dir($full) ) 
                {
                    rmdir($full);
                }
                else 
                {
                    unlink($full);
                }
            }
        }
    }
?>
</body>
</html>