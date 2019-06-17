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
    <link rel="stylesheet" href="./css/style.css">
	<style type="text/css">
           

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
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="compare.php">Compare<span class="sr-only">(current)</span></a>
                </li>
            </ul>
        </div>
    </nav>

     <main role="main" class="container" style="
    margin-left: 20px;">
	<div>
		<div id="form-container">
			<h4>Compare Two Files:</h4>
			<form action="compareOutput.php" method="post" enctype="multipart/form-data">
				<div class="form-group"><input type="file" class="form-control px-3" name="fileToUpload" id="fileToUpload" accept=".apk, .ipa"></div>
				<div class="form-group"><input type="file" class="form-control px-3" name="fileToUpload1" id="fileToUpload1" accept=".apk, .ipa"></div>
				<div class="form-group"><input type="hidden" name="sslCheck" value="checked"><input type="hidden" name="manifestCheck" value="checked"><input type="hidden" name="certificateCheck" value="checked">  <input type="hidden" name="infoCheck" value="checked"><input type="hidden" name="embeddedCheck" value="checked"><input type="hidden" id="compareTwoFiles" name="compareTwoFiles" value="checked"> </div><span class="input-group-btn">
				<button type="submit" name="submit" style="float: right;" class="btn btn-secondary btn-form display-4">Upload APK/IPA</button><br>
				</span>
			</form>
			<h4>Compare with Log:</h4>
			<br><form action="compareOutput.php" method="post" enctype="multipart/form-data">
				<div class="form-group"><input type="file" class="form-control px-3" name="fileToUpload" id="fileToUpload" accept=".apk, .ipa"></div>
				<div class="form-group"><input type="hidden" name="sslCheck" value="checked"> <input type="hidden" name="manifestCheck" value="checked">  <input type="hidden" name="certificateCheck" value="checked">  <input type="hidden" name="infoCheck" value="checked"><input type="hidden" name="embeddedCheck" value="checked">  <input type="hidden" id="compareWithLog" name="compareWithLog" value="checked"> </div><span class="input-group-btn">
				<button type="submit" name="submit" style="float: right;" class="btn btn-secondary btn-form display-4">Upload APK/IPA</button>
				</span>
			</form>
		</div>
	</div>
        <div>            
            <p class = "lead"><a href = "index.php">Return to Home Page</a></p>   
        </div>
	<?php
            function removeDirectory($path) 
            {
                $files = glob($path . '/*');
                foreach ($files as $file) 
                {
                    is_dir($file) ? removeDirectory($file) : unlink($file);
                }
                return;
            }
            
        
            if(file_exists("uploads"))
            {
                removeDirectory("uploads");
            }

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