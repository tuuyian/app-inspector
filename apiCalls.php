<?php
	include 'openFile.php';
	require_once './vendor/class.Diff.php';
	require_once __DIR__ . '/vendor/autoload.php';
	if (isset($_POST["compareTwoFiles"]))
	{
		$tmpName = $_FILES["fileToUpload"]["tmp_name"];
		$tmpName1 = $_FILES["fileToUpload1"]["tmp_name"];
		$firstUpload = basename($_FILES["fileToUpload"]["name"]);
		$secondUpload = basename($_FILES["fileToUpload1"]["name"]);
		$textFile1 = fileUpload($firstUpload, $tmpName);
		$textFile2 = fileUpload($secondUpload, $tmpName1);
		
		if( $textFile1 != false && $textFile2 != false)
		{
			$myfile = fopen("logs/temp/". $textFile1, "r") or die("Unable to open file!");
			$stringBean = fread($myfile,filesize("logs/temp/". $textFile1));
			fclose($myfile);
			
			$myfile1 = fopen("logs/temp/". $textFile2, "r") or die("Unable to open file!");
			$stringBean1 = fread($myfile1,filesize("logs/temp/". $textFile2));
			fclose($myfile1);
			
			$fileDiff = Diff::toString(Diff::compareFiles("logs/temp/". $textFile1, "logs/temp/". $textFile2));
			$handle = file_put_contents("logs/temp/diff.txt",$fileDiff);
			
			if (isset($_POST["CMD"]))
			{
				$diffText = fopen("logs/temp/diff.txt", "r") or die("Unable to open file!");
				$stringBean2 = fread($diffText,filesize("logs/temp/diff.txt"));
				fclose($diffText);
				$a = strip_tags($stringBean2);
				echo "\n" . $a;
			}
		}
		
	}
	if (isset($_POST["compareWithLog"]))
	{
		$tmpName = $_FILES["fileToUpload"]["tmp_name"];
		$firstUpload = basename($_FILES["fileToUpload"]["name"]);
		$textFile1 = fileUpload($firstUpload, $tmpName);
		$appFileType = strtolower(pathinfo($firstUpload,PATHINFO_EXTENSION));
		$myfile = fopen("logs/temp/". $textFile1, "r") or die("Unable to open file!");
		$stringBean = fread($myfile,filesize("logs/temp/". $textFile1));
		fclose($myfile);
		
		if( $textFile1 != false)
		{
			if ($appFileType == "apk")
			{
				$myfile1 = fopen("logs/submittedAPKLog.txt", "r") or die("Unable to open file!");
				$stringBean1 = fread($myfile1,filesize("logs/submittedAPKLog.txt"));
				$submittedLog = "logs/submittedAPKLog.txt";
				fclose($myfile1);
			}
			
			else if($appFileType == "ipa")
			{
				$myfile1 = fopen("logs/submittedIPALog.txt", "r") or die("Unable to open file!");
				$stringBean1 = fread($myfile1,filesize("logs/submittedIPALog.txt"));
				$submittedLog = "logs/submittedIPALog.txt";
				fclose($myfile1);
			}
			
			
			$fileDiff = Diff::toString(Diff::compareFiles("logs/temp/". $textFile1, $submittedLog));
			$handle = file_put_contents("logs/temp/diff.txt",$fileDiff);
			if (isset($_POST["CMD"]))
			{
				$diffText = fopen("logs/temp/diff.txt", "r") or die("Unable to open file!");
				$stringBean2 = fread($diffText,filesize("logs/temp/diff.txt"));
				fclose($diffText);
				$a = strip_tags($stringBean2);
				echo "\n" . $a;
			}
		}
		
	}
	
	if(isset($_POST["uploadOnly"]))
	{
			$tmpName = $_FILES["fileToUpload"]["tmp_name"];
			$firstUpload = basename($_FILES["fileToUpload"]["name"]);
			$uploadedFile = fileUpload($firstUpload, $tmpName);
			
			if( $uploadedFile != false)
			{
				$myfile1 = fopen("logs/temp/". $uploadedFile, "r") or die("Unable to open file!");
				$cmdOutput = fread($myfile1,filesize("logs/temp/". $uploadedFile));
				fclose($myfile1);
				
				$cmdOutput = strip_tags($cmdOutput);
				echo $cmdOutput;
			}
	}
	
	function fileUpload($fileToUpload, $tempname)
	{	
		mkdir("uploads/". $fileToUpload, 0770, true);
		//Setting Variables for file upload
		$target_dir = "uploads/". $fileToUpload . "/";
		$target_file = $target_dir . $fileToUpload;
		$tmp_name = $tempname;
		$uploadOk = 1;
		$appFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
		$filename =  "usablename." . $appFileType;
		$zip = new ZipArchive;
		$fileOutput = "";

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
				//echo "<p class = 'lead'>The file ". $fileToUpload. " has been uploaded.</p>";
			} else {
				echo "<p class = 'lead'>Sorry, there was an error uploading your file.</p>";
			}
		}

			set_error_handler(function ($err_severity, $err_msg, $err_file, $err_line, array $err_context)
		{
			throw new ErrorException( $err_msg, 0, $err_severity, $err_file, $err_line );
		}, E_WARNING);
		//Pull specific information based on type of file
		if($appFileType == "apk")
		{
			$fileOutput = appendInfo($fileOutput, "<h4 style='margin:0;display:inline'>Filename: " . $fileToUpload ."</h4><br><br> \r\n");
			
			//Opening the APK file using ZipArchive
			$path = $target_dir . $filename;
			
			if ($zip->open($path))
			{				
				//Checking locations for SSL pinning using specifc strings as well as checking the classes for specific type of pinning
				
				$fileOutput = appendInfo($fileOutput, "<br><h4>SSL Pinning:</h4><br> \r\n");
				$fileOutput = appendInfo($fileOutput,"<p class = 'lead' style='margin:0;display:inline'>\r\n");
				try
				{
					if ($zip->getFromName('okhttp3/internal/publicsuffix/publicsuffixes.gz')!== false)
					{
						//echo "<p class = 'lead'>Pinned using OkHttp3</p>";
						$fileOutput = appendInfo($fileOutput, "<p class = 'lead' style='margin:0;display:inline'>Pinned using OkHttp3</p><br> \r\n");
					}
					else
					{
						$fileinfo = pathinfo('classes.dex');
						copy("zip://".realpath($path)."#classes.dex", $target_dir .$fileinfo['basename']);
						if(exec("dexdump " . $target_dir ."classes.dex | findstr /r \"SSLContext\" 2>&1")!== '')
						{
							//echo "<p class = 'lead'>Pinned using HttpsURLConnection</p>";
							$fileOutput = appendInfo($fileOutput, "<p class = 'lead' style='margin:0;display:inline'>Pinned using HttpsURLConnection</p><br> \r\n");
						}
						else
						{
							//echo "<p class = 'lead'>No SSL Pinning</p>";
								$fileOutput = appendInfo($fileOutput, "<p class = 'lead' style='margin:0;display:inline'>No SSL Pinning</p><br> \r\n");
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
				catch (Exception $e)
				{
					echo "Error incorrect file type!";
					return false;
				}
				restore_error_handler();
			}
			else
			{
				echo "<p class = 'lead'>Cannot Read APK</p>";
			}
			//Using AXMLPrinter2 to parse the androidmanifest, making it readable and easy to pull information.
			
			exec("java -jar axmlprinter2.jar " . $target_dir . "AndroidManifest.xml > ". $target_dir ."ParsedAndroidManifest.xml");
			//echo "<h4>Android Manifest Details:</h4>";
			$fileOutput = appendInfo($fileOutput, "</p><h4>Android Manifest Details:</h4><br> \r\n");
			error_reporting(E_ERROR | E_PARSE);
			$dom = new DOMDocument();
			$dom->load($target_dir . 'ParsedAndroidManifest.xml');
			$xml = simplexml_import_dom($dom);
			$versionName = $xml->xpath('/manifest/@android:versionName');
			$versionCode =$xml->xpath('/manifest/@android:versionCode');
			$package = $xml->xpath('/manifest/@package');
			//echo "<p class = 'lead'><br>Version Name :".$versionName[0]->versionName."<br/>";
			$fileOutput = appendInfo($fileOutput, "<p class = 'lead' style='margin:0;display:inline'>Version Name :".$versionName[0]->versionName."</p><br/> \r\n");
			//echo "Version Code :".$versionCode[0]->versionCode."<br/>";
			$fileOutput = appendInfo($fileOutput, "<p class = 'lead' style='margin:0;display:inline'>Version Code :".$versionCode[0]->versionCode."</p><br/> \r\n");
			//echo "Package Name :".$package[0]->package."<br/></p>";
			$fileOutput = appendInfo($fileOutput, "<p class = 'lead' style='margin:0;display:inline'>Package Name :".$package[0]->package."</p><br> \r\n");
			
			

			//Print out Certficate information
			
			exec("keytool -printcert -file ". $target_dir ."CERT.RSA", $certs);
			//echo "<br><h4>Certificates:</h4>";
			$fileOutput = appendInfo($fileOutput, "<br><h4>Certificates:</h4> \r\n");
			$fileOutput = appendInfo($fileOutput, "<p class = 'lead'>");
			//echo implode("<br>" , $certs); 
			$fileOutput = appendInfo($fileOutput, implode ("<br>\r\n", $certs));
			
			
			if(isset($_POST["logoCheck"]))
			{
				echo "</p><br><h4>Logo:</h4><img src = '". $target_dir ."/icon.png'>";
			}
			
			if(isset($_POST["uploadOnly"]))
			{
				$filename = 'apkLog.txt';
				$handle = file_put_contents("logs/temp/" . $filename,$fileOutput);
				return $filename;
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
				//$infoPlist = plist::Parse($target_dir.'Info.plist');
				$embedded = plist::Parse($target_dir.'parsed.mobileprovision');
				$content = file_get_contents($target_dir.'Info.plist');
				$plist = new CFPropertyList\CFPropertyList();
				$plist->parse($content);
				$infoPlist = $plist->toArray(); 
				
				
				$fileOutput = appendInfo($fileOutput, "<p class = 'lead' style='margin:0;display:inline'><h4><b>Info.plist Information:</b></h4><br>\r\n Build Machine OS Build: " . $infoPlist["BuildMachineOSBuild"] . "<br>\r\n CF Bundle Development Region: " . $infoPlist["CFBundleDevelopmentRegion"] . "<br>\r\n CF Bundle Display Name: " . $infoPlist["CFBundleDisplayName"]. "<br>\r\n CF Bundle Executable: " . $infoPlist["CFBundleExecutable"]. "<br>\r\n CF Bundle Identifier: " . $infoPlist["CFBundleIdentifier"]."<br>\r\n CF Bundle Info Dictionary Version: " . $infoPlist["CFBundleInfoDictionaryVersion"]."<br>\r\n CF Bundle Short Version String: " . $infoPlist["CFBundleShortVersionString"]."<br>\r\n Minimum OS Version: " . $infoPlist["MinimumOSVersion"]."<br></p>\r\n");
				/* echo "<p class = 'lead'><h4><b>Info.plist Information:</b></h4>";
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
				echo "<br>"; */
			
			
			
				$fileOutput = appendInfo($fileOutput, "<p class = 'lead' style='margin:0;display:inline'><br><h4><b>Embedded.mobileprovision Information:</b></h4><br>\r\n App ID Name: " . $embedded["AppIDName"]."<br>\r\n Application Identifier Prefix: " . $embedded["ApplicationIdentifierPrefix"][0]."<br>\r\n Creation Date: " . $embedded["CreationDate"]."<br>\r\n Platform: " . $embedded["Platform"][0]."<br>\r\n Developer Certificates: " . (string)$embedded["DeveloperCertificates"][0]."<br>\r\n <b>Entitlements:</b> <br>\r\n Keychain-Access-Groups: " . $embedded["Entitlements"]["keychain-access-groups"][0]."<br>\r\n Application-Identifier: " . $embedded["Entitlements"]["application-identifier"]."<br>\r\n com.apple.developer.Team-Identifier: " . $embedded["Entitlements"]["com.apple.developer.team-identifier"]."<br>\r\n APS-Environment: " . $embedded["Entitlements"]["aps-environment"]."<br>\r\n Expiration Date: " . $embedded["ExpirationDate"]."<br>\r\n Name: " . $embedded["Name"]."<br>\r\n Team Name: " . $embedded["TeamName"]."<br>\r\n Time To Live: " . $embedded["TimeToLive"]."<br>\r\n UUID: " . $embedded["UUID"]."<br>\r\n Version: " . $embedded["Version"]."<br></p>\r\n");
				/* echo "<br><h4><b>Embedded.mobileprovision Information:</b></h4>";
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
				echo "<br></p>"; */
				
			}
			else
			{
				echo "<p class = 'lead'>Cannot Read IPA</p>";
			}
			/*TODO: Parsing the info.plist and embedded.mobileprovision. Also to find where to pull the logo.
			  NOTE: exec(openssl smime -inform der -verify -noverify -in embedded.mobileprovision) > to parse mobileprovision
			*/
			
			if(isset($_POST["uploadOnly"]))
			{
				$filename = 'ipaLog.txt';
				$handle = file_put_contents("logs/temp/" . $filename,$fileOutput);
				return $filename;
			}	
		}
		
		$filename = $fileToUpload . ".txt";
		$handle = file_put_contents("logs/temp/" . $filename,$fileOutput);
			
		return $filename;
	}
	
		
    ?>