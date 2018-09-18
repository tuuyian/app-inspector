<!DOCTYPE html>
<html>
<body>


<?php
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $filename = basename($_FILES["fileToUpload"]["name"]);
    $tmp_name = $_FILES["fileToUpload"]["tmp_name"];
    $uploadOk = 1;
    $appFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    $zip = new ZipArchive;
    

    // Check if file already exists
    if (file_exists($target_file)) {
        echo "Sorry, file already exists.</br>";
        $uploadOk = 0;
    }
    // Allow certain file formats
    if($appFileType != "apk" && $appFileType != "ipa") {
        echo "Sorry, only APK & IPA files are allowed.";
        $uploadOk = 0;
    }
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    // if everything is ok, try to upload file

    } else {
        if (move_uploaded_file($tmp_name, $target_file)) {
            echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
    
    $path = 'uploads/' . $filename;
    if ($zip->open($path)) 
    {
        if ($zip->getFromName('res/drawable/icon.png')!== false)
        {
            $fileinfo = pathinfo('res/drawable/icon.png');
            copy("zip://".realpath($path)."#res/drawable/icon.png", "uploads/".$fileinfo['basename']);
        }
        else if ($zip->getFromName('res/drawable-hdpi-v4/icon.png')!== false)
        {
            $fileinfo = pathinfo('res/drawable-hdpi-v4/icon.png');
            copy("zip://".realpath($path)."#res/drawable-hdpi-v4/icon.png", "uploads/".$fileinfo['basename']);
        }
        else
        {
            $fileinfo = pathinfo('res/drawable-hdpi/icon.png');
            copy("zip://".realpath($path)."#res/drawable-hdpi/icon.png", "uploads/".$fileinfo['basename']);
        }
                
        echo "<br><h4>SSL Pinning:</h4>";
        if ($zip->getFromName('okhttp3/internal/publicsuffix/publicsuffixes.gz')!== false)
        {
            echo "Pinned using OkHttp3";    
        }
        else
        {
            $fileinfo = pathinfo('classes.dex');
            copy("zip://".realpath($path)."#classes.dex", "uploads/".$fileinfo['basename']);
            if(exec("dexdump uploads/classes.dex | findstr /r \"SSLContext\" 2>&1")!== '')
            {
                echo "Pinned using HttpsURLConnection";
            }
            else
            {
                echo "No SSL Pinning";
            }
            
        }
        $zip->close();
    } 
    else 
    {
        echo 'Cannot Read APK/IPA';
    }
    
    if($appFileType == "apk")
    {
        echo "<h4>Android Manifest Details:</h4>";
        echo exec("node apkRead.js " . $filename, $output);
        implode("\n" , $output);
        
        exec("java -jar apksigner.jar verify --print-certs uploads/". $filename , $certs);
        echo "<br><h4>Certificates:</h4>";
        echo implode("<br>", $certs);
        
    }
    
    else if($appFileType == "ipa")
    {
        exec("node ipaRead.js " . $filename, $output); 
        echo implode("<br>", $output);
    }
    
    if(count(glob("uploads/*"))!=0) 
    {
        if (time() - filectime('uploads/'.$filename) > 5) 
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
    }
    echo "<br><h4>Logo:</h4><img src='uploads/icon.png'>";
    ?>
    
    <p><a href = 'index.php'>Return to Home Page</a></p>
</body>
</html>



