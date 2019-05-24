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
			
			 if(file_exists("logs/temp"))
            {
                removeDirectory("logs/temp");
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
    