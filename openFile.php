<?php

function printInfo($fileOutput, $print)
{
                   /* echo "<p class = 'lead'>Pinned using OkHttp3</p>";
                    $fileOutput "<p class = 'lead'>Pinned using OkHttp3</p>";

                    echo "<p class = 'lead'>Pinned using HttpsURLConnection</p>";
                    $fileoutput .= "<p class = 'lead'>Pinned using HttpsURLConnection</p>";*/

                    echo $print;
                    $fileOutput .= $print ."\r\n";

                    return $fileOutput;
}

function appendInfo($fileOutput, $print)
{
                   /* echo "<p class = 'lead'>Pinned using OkHttp3</p>";
                    $fileOutput "<p class = 'lead'>Pinned using OkHttp3</p>";

                    echo "<p class = 'lead'>Pinned using HttpsURLConnection</p>";
                    $fileoutput .= "<p class = 'lead'>Pinned using HttpsURLConnection</p>";*/

                    $fileOutput .= $print ."\r\n";

                    return $fileOutput;
}


?>