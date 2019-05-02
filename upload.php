<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../../../favicon.ico">
    
    <title>Application Inspector</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>

    
<body>

    <nav class="navbar navbar-expand-md navbar-dark fixed-top">
        <a class="navbar-brand" href="index.php">Application Inspector</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

        <div class="collapse navbar-collapse" id="navbarsExampleDefault">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="index.php">Home<span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Compare</a>
                </li>
            </ul>
        </div>
    </nav>

     <main role="main" class="container" style="
    margin-left: 20px;">
<?php
    require_once __DIR__ . '/vendor/autoload.php';
    mkdir("uploads/". basename( $_FILES["fileToUpload"]["name"]), 0770, true);
	//Setting Variables for file upload
    $target_dir = "uploads/". basename( $_FILES["fileToUpload"]["name"]). "/";
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $tmp_name = $_FILES["fileToUpload"]["tmp_name"];
    $uploadOk = 1;
    $appFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
	$filename =  "usablename." . $appFileType;
    $zip = new ZipArchive;


    // Check if file already exists in the folder
    if (file_exists($target_file)) {
        echo "<p class = 'lead'>Sorry, file already exists.</p></br>";
        $uploadOk = 0;
    }
    // Allow only APK/IPA file formats
    if($appFileType != "apk" && $appFileType != "ipa") {
        echo "<p class = 'lead'>Sorry, only APK & IPA files are allowed.</p>";
        $uploadOk = 0;
    }
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "<p class = 'lead'>Sorry, your file was not uploaded.</p>";
    // If checks are passed then try to upload the file

    } else {
        if (move_uploaded_file($tmp_name, $target_dir . $filename)) {
            echo "<p class = 'lead'>The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.</p>";
        } else {
            echo "<p class = 'lead'>Sorry, there was an error uploading your file.</p>";
        }
    }


    //Pull specific information based on type of file
    if($appFileType == "apk")
    {
		//Opening the APK file using ZipArchive
		$path = $target_dir . $filename;
		if ($zip->open($path))
		{
			//Trying to find the logo of an application in multiple locations and then unzip the logo to the upload folder
			if ($zip->getFromName('res/drawable/icon.png')!== false)
			{
				$fileinfo = pathinfo('res/drawable/icon.png');
				copy("zip://".realpath($path)."#res/drawable/icon.png", $target_dir .$fileinfo['basename']);
			}
			else if ($zip->getFromName('res/drawable-hdpi-v4/icon.png')!== false)
			{
				$fileinfo = pathinfo('res/drawable-hdpi-v4/icon.png');
				copy("zip://".realpath($path)."#res/drawable-hdpi-v4/icon.png", $target_dir .$fileinfo['basename']);
			}
			else
			{
				$fileinfo = pathinfo('res/drawable-hdpi/icon.png');
				copy("zip://".realpath($path)."#res/drawable-hdpi/icon.png", $target_dir .$fileinfo['basename']);
			}

			//CHecking locations for SSL pinning using specifc strings as well as checking the classes for specific type of pinning
            if (isset($_POST["sslCheck"]))
            {
                echo "<br><h4>SSL Pinning:</h4>";
                if ($zip->getFromName('okhttp3/internal/publicsuffix/publicsuffixes.gz')!== false)
                {
                    echo "<p class = 'lead'>Pinned using OkHttp3</p>";
                }
                else
                {
                    $fileinfo = pathinfo('classes.dex');
                    copy("zip://".realpath($path)."#classes.dex", $target_dir .$fileinfo['basename']);
                    if(exec("dexdump " . $target_dir ."classes.dex | findstr /r \"SSLContext\" 2>&1")!== '')
                    {
                        echo "<p class = 'lead'>Pinned using HttpsURLConnection</p>";
                    }
                    else
                    {
                        echo "<p class = 'lead'>No SSL Pinning</p>";
                    }
			    } 
            }
            
			
			//Extract Certificates to be read
			if ($zip->getFromName('META-INF/CERT.RSA')!== false)
			{

				$fileinfo = pathinfo('META-INF/CERT.RSA');
				copy("zip://".realpath($path)."#META-INF/CERT.RSA", "".$target_dir."/CERT.RSA");
			}
			else if ($zip->getFromName('META-INF/AND-PROD.RSA')!== false)
			{
				$fileinfo = pathinfo('META-INF/AND-PROD.RSA');
				copy("zip://".realpath($path)."#META-INF/AND-PROD.RSA", $target_dir. "CERT.RSA");
			}
			//Extract the Android Manifest from the APK and place it in the uploads folder to be read
			$fileinfo = pathinfo('AndroidManifest.xml');
			copy("zip://".realpath($path)."#AndroidManifest.xml", $target_dir .$fileinfo['basename']);
			$zip->close();
		}
		else
		{
			echo "<p class = 'lead'>Cannot Read APK</p>";
		}
		//Using AXMLPrinter2 to parse the androidmanifest, making it readable and easy to pull information.
        if(isset($_POST["manifestCheck"]))
        {
            exec("java -jar axmlprinter2.jar " . $target_dir . "AndroidManifest.xml > ". $target_dir ."ParsedAndroidManifest.xml");
            echo "<h4>Android Manifest Details:</h4>";
            error_reporting(E_ERROR | E_PARSE);
            $dom = new DOMDocument();
            $dom->load($target_dir . 'ParsedAndroidManifest.xml');
            $xml = simplexml_import_dom($dom);
            $versionName = $xml->xpath('/manifest/@android:versionName');
            $versionCode =$xml->xpath('/manifest/@android:versionCode');
            $package = $xml->xpath('/manifest/@package');
            echo "<p class = 'lead'><br>Version Name :".$versionName[0]->versionName."<br/>";
            echo "Version Code :".$versionCode[0]->versionCode."<br/>";
            echo "Package Name :".$package[0]->package."<br/></p>";
        }
		

    //Print out Certficate information
        if(isset($_POST["certificateCheck"]))
        {
            exec("keytool -printcert -file ". $target_dir ."CERT.RSA", $certs);
            echo "<br><h4>Certificates:</h4>";
            echo "<p class = 'lead'";
            echo implode("<br>" , $certs); 
        }
        
        if(isset($_POST["logoCheck"]))
        {
            echo "</p><br><h4>Logo:</h4><img src = '". $target_dir ."/icon.png'>";
        }
       
    }

    else if($appFileType == "ipa")
    {
		//Unzipping IPA, trying to find the location of the info.plist and mobileprovision. Have to find the specfic app name within the Payload folder.
		$path = $target_dir . $filename;
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
				}
				break;
			}
			else
			{
				continue;
			}
        }
		//Extracting the plist and mobileprovision
			$fileinfo = pathinfo('Payload/' . $appName . "/Info.plist");
			copy("zip://".realpath($path)."#Payload/" . $appName . "/Info.plist", $target_dir.$fileinfo['basename']);
			$fileinfo = pathinfo('Payload/' . $appName . "/embedded.mobileprovision");
			copy("zip://".realpath($path)."#Payload/" . $appName . "/embedded.mobileprovision", $target_dir.$fileinfo['basename']);
            exec("openssl smime -inform der -verify -noverify -in ".$target_dir."embedded.mobileprovision > ".$target_dir."parsed.mobileprovision");
            $embedded = plist::Parse($target_dir.'parsed.mobileprovision');
            $content = file_get_contents($target_dir.'Info.plist');
			$plist = new CFPropertyList\CFPropertyList();
			$plist->parseBinary($content);
			$infoPlist = $plist->toArray();
			
            if(isset($_POST["infoCheck"]))
            {
                echo "<p class = 'lead'><h4><b>Info.plist Information:</b></h4>";
                echo "Build Machine OS Build: " . $infoPlist["BuildMachineOSBuild"];
                echo "<br>";
                echo "CF Bundle Development Region: " . $infoPlist["CFBundleDevelopmentRegion"];
                echo "<br>";
                echo "CF Bundle Display Name: " . $infoPlist["CFBundleDisplayName"];
                echo "<br>";
                echo "CF Bundle Executable: " . $infoPlist["CFBundleExecutable"];
                echo "<br>";
                echo "CF Bundle Identifier: " . $infoPlist["CFBundleIdentifier"];
                echo "<br>";
                echo "CF Bundle Info Dictionary Version: " . $infoPlist["CFBundleInfoDictionaryVersion"];
                echo "<br>";
                echo "CF Bundle Short Version String: " . $infoPlist["CFBundleShortVersionString"];
                echo "<br>";
                echo "Minimum OS Version: " . $infoPlist["MinimumOSVersion"];
                echo "<br>";
            }
            
            if(isset($_POST["embeddedCheck"]))
            {
                echo "<br><h4><b>Embedded.mobileprovision Information:</b></h4>";
                echo "App ID Name: " . $embedded["AppIDName"];
                echo "<br>";
                echo "Application Identifier Prefix: " . $embedded["ApplicationIdentifierPrefix"][0];
                echo "<br>";
                echo "Creation Date: " . $embedded["CreationDate"];
                echo "<br>";
                echo "Platform: " . $embedded["Platform"][0];
                echo "<br>";
                echo "Developer Certificates: " . (string)$embedded["DeveloperCertificates"][0];
                echo "<br>";
                echo "<b>Entitlements:</b> <br>";
                echo "Keychain-Access-Groups: " . $embedded["Entitlements"]["keychain-access-groups"][0];
                echo "<br>";
                echo "Application-Identifier: " . $embedded["Entitlements"]["application-identifier"];
                echo "<br>";
                echo "com.apple.developer.Team-Identifier: " . $embedded["Entitlements"]["com.apple.developer.team-identifier"];
                echo "<br>";
                echo "APS-Environment: " . $embedded["Entitlements"]["aps-environment"];
                echo "<br>";
                echo "Expiration Date: " . $embedded["ExpirationDate"];
                echo "<br>";
                echo "Name: " . $embedded["Name"];
                echo "<br>";
                echo "Team Name: " . $embedded["TeamName"];
                echo "<br>";
				echo "Time To Live: " . $embedded["TimeToLive"];
                echo "<br>";
                echo "UUID: " . $embedded["UUID"];
                echo "<br>";
                echo "Version: " . $embedded["Version"];
                echo "<br></p>";
            }
            
            
            
            
		}
		else
		{
			echo "<p class = 'lead'>Cannot Read IPA</p>";
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
        <div>            
            <p class = "lead"><a href = "index.php">Return to Home Page</a></p>   
        </div>

    </main>
    <!-- /.container -->
    
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

</body>
</html>