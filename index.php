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
        <a class="navbar-brand" href="booking.html">Application Inspector</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

        <div class="collapse navbar-collapse" id="navbarsExampleDefault">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="index.html">Home<span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="compare.html">Compare</a>
                </li>
            </ul>
        </div>
    </nav>

    <main role="main" class="container">
        
        <div class="landing">
            <h1 id = "landingHeader">Fiserv Application Inspector</h1>
            <p id = "landingParagraph"class="lead">This tool is used to help verify the data integrity of built mobile applications. <br><br>Please upload an APK or IPA below!</p>
        </div>
        <div id = displayInfo>
            <button class="btn btn-primary" onclick="uploadAPK()">APK</button>
            <button class="btn btn-primary" onclick="uploadIPA()">IPA</button>
        <!--<div id="form-container">
                <form action="upload.php" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <input type="file" class="form-control px-3" name="fileToUpload" id="fileToUpload" accept=".apk, .ipa">
                        </div>
                        <div class="form-group">
                            <input type="checkbox" name="infoCheck" value="checked"> Info.Plist <br>
                            <input type="checkbox" name="embeddedCheck" value="checked"> Embedded.mobileprovision <br>
                        </div>
                    <span class="input-group-btn"><button type="submit" name="submit" style="float: right;" class="btn btn-secondary btn-form display-4">Upload APK/IPA</button></span>
                </form>
         </div>-->
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
    <script type="text/javascript" src="./js/dom.js"></script> 
</body>
</html>