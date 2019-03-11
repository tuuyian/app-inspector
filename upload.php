<!DOCTYPE html>
<html>
<body>


<?php
	//Setting Variables for file upload
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $tmp_name = $_FILES["fileToUpload"]["tmp_name"];
    $uploadOk = 1;
    $appFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
	$filename =  "usablename." . $appFileType;
    $zip = new ZipArchive;


    // Check if file already exists in the folder
    if (file_exists($target_file)) {
        echo "Sorry, file already exists.</br>";
        $uploadOk = 0;
    }
    // Allow only APK/IPA file formats
    if($appFileType != "apk" && $appFileType != "ipa") {
        echo "Sorry, only APK & IPA files are allowed.";
        $uploadOk = 0;
    }
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    // If checks are passed then try to upload the file

    } else {
        if (move_uploaded_file($tmp_name, $target_dir . $filename)) {
            echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }


    //Pull specific information based on type of file
    if($appFileType == "apk")
    {
		//Opening the APK file using ZipArchive
		$path = 'uploads/' . $filename;
		if ($zip->open($path))
		{
			//Trying to find the logo of an application in multiple locations and then unzip the logo to the upload folder
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

			//CHecking locations for SSL pinning using specifc strings as well as checking the classes for specific type of pinning
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
			//Extract Certificates to be read
			if ($zip->getFromName('META-INF/CERT.RSA')!== false)
			{

				$fileinfo = pathinfo('META-INF/CERT.RSA');
				copy("zip://".realpath($path)."#META-INF/CERT.RSA", "uploads/CERT.RSA");
			}
			else if ($zip->getFromName('META-INF/AND-PROD.RSA')!== false)
			{
				$fileinfo = pathinfo('META-INF/AND-PROD.RSA');
				copy("zip://".realpath($path)."#META-INF/AND-PROD.RSA", "uploads/CERT.RSA");
			}
			//Extract the Android Manifest from the APK and place it in the uploads folder to be read
			$fileinfo = pathinfo('AndroidManifest.xml');
			copy("zip://".realpath($path)."#AndroidManifest.xml", "uploads/".$fileinfo['basename']);
			$zip->close();
		}
		else
		{
			echo 'Cannot Read APK';
		}
		//Using AXMLPrinter2 to parse the androidmanifest, making it readable and easy to pull information.
		exec("java -jar axmlprinter2.jar uploads/AndroidManifest.xml > uploads/ParsedAndroidManifest.xml");
    echo "<h4>Android Manifest Details:</h4>";
		error_reporting(E_ERROR | E_PARSE);
		$dom = new DOMDocument();
		$dom->load('uploads/ParsedAndroidManifest.xml');
		$xml = simplexml_import_dom($dom);
		$versionName = $xml->xpath('/manifest/@android:versionName');
		$versionCode =$xml->xpath('/manifest/@android:versionCode');
		$package = $xml->xpath('/manifest/@package');
		echo "<br>Version Name :".$versionName[0]->versionName."<br/>";
		echo "Version Code :".$versionCode[0]->versionCode."<br/>";
		echo "Package Name :".$package[0]->package."<br/>";

    //Print out Certficate information
    exec("keytool -printcert -file uploads/CERT.RSA", $certs);
    echo "<br><h4>Certificates:</h4>";
    echo implode("<br>" , $certs);

    }

    else if($appFileType == "ipa")
    {
		//Unzipping IPA, trying to find the location of the info.plist and mobileprovision. Have to find the specfic app name within the Payload folder.
		$path = 'uploads/' . $filename;
		$zip = zip_open($path);
		if(is_resource($zip)) {

		while (($zip_entry = zip_read($zip)))
        {
			$appName = zip_entry_name($zip_entry);
			if (strpos($appName, ".app") !== false)
			{
				if(($pos = strpos($appName, '/')) !== false)
				{
				   $appName = substr($appName, $pos + 1);
				   $appName = strstr($appName, '/', true);
				}
				else
				{
				   echo "im here";
				}
				echo $appName;
				break;
			}
			else
			{
				continue;
			}
        }
		//Extracting the plist and mobileprovision
			$fileinfo = pathinfo('P-ayload/' . $appName . "/Info.plist");
			copy("zip://".realpath($path)."#Payload/" . $appName . "/Info.plist", "uploads/".$fileinfo['basename']);
			$fileinfo = pathinfo('Payload/' . $appName . "/embedded.mobileprovision");
			copy("zip://".realpath($path)."#Payload/" . $appName . "/embedded.mobileprovision", "uploads/".$fileinfo['basename']);
		}
		else
		{
			echo 'Cannot Read IPA';
		}
		/*TODO: Parsing the info.plist and embedded.mobileprovision. Also to find where to pull the logo.
		  NOTE: exec(openssl smime -inform der -verify -noverify -in embedded.mobileprovision) > to parse mobileprovision
		*/

    }

    /* Timed delete script to be implemented.

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
    } */

    ?>

    <p><a href = 'index.php'>Return to Home Page</a></p>
</body>
</html>
