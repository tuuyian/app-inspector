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
    <link rel="stylesheet" href="css/style.css">
	<style type="text/css">
        .btn-primary {
            height: 50px;
			
        }     
		#displayInfo {
            text-align: left;
        }  
    </style>
</head>

    
<body>

    <nav class="navbar navbar-expand-md navbar-dark fixed-top">
        <a class="navbar-brand" href="index.php">Application Inspector</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

        <div class="collapse navbar-collapse" id="navbarsExampleDefault">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home<span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="compare.php">Compare</a>
                </li>
            </ul>
        </div>
    </nav>

     <main role="main" class="container" style="
    margin-left: 20px;">
	<button class="btn btn-primary" onclick="file1()">File 1 Info</button>
    <button class="btn btn-primary" onclick="file2()">File 2 Info</button>
	<button class="btn btn-primary" onclick="diff()">Diff</button><br><br>
	<div id = "displayInfo"></div>
<?php
	include 'openFile.php';
	include 'uploadFunction.php';
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
		
		$myfile = fopen("logs/temp/". $textFile1, "r") or die("Unable to open file!");
		$stringBean = fread($myfile,filesize("logs/temp/". $textFile1));
		fclose($myfile);
		
		$myfile1 = fopen("logs/temp/". $textFile2, "r") or die("Unable to open file!");
		$stringBean1 = fread($myfile1,filesize("logs/temp/". $textFile2));
		fclose($myfile1);
		
		$fileDiff = Diff::toString(Diff::compareFiles("logs/temp/". $textFile1, "logs/temp/". $textFile2));
		$handle = file_put_contents("logs/temp/diff.txt",$fileDiff);

	}
	if (isset($_POST["compareWithLog"]))
	{
		$tmpName = $_FILES["fileToUpload"]["tmp_name"];
		$firstUpload = basename($_FILES["fileToUpload"]["name"]);
		$textFile1 = fileUpload($firstUpload, $tmpName);
		$appFileType = strtolower(pathinfo($firstUpload,PATHINFO_EXTENSION));
		 $appFileType;
		$myfile = fopen("logs/temp/". $textFile1, "r") or die("Unable to open file!");
		$stringBean = fread($myfile,filesize("logs/temp/". $textFile1));
		fclose($myfile);
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
		
	}
	
		
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
	<script type="text/javascript">
		diff();
		function file1(){
		var bool = <?php echo json_encode($stringBean, JSON_HEX_TAG)?>; 
		document.getElementById("displayInfo").innerHTML = bool;
		}
		function file2(){
		var bool = <?php echo json_encode($stringBean1, JSON_HEX_TAG)?>; 
		document.getElementById("displayInfo").innerHTML = bool;
		}
		function diff(){
		var bool = <?php echo json_encode($fileDiff, JSON_HEX_TAG)?>; 
		document.getElementById("displayInfo").innerHTML = bool;
		}
	
	</script>

</body>
</html>