<?php
//Name:			centralplainsvisible.php
//Description:	Builds a combined image from several images of the radar on the central plains.
//			It then displays the image in HTML or direct as image or just builds the image file.
//			Files come from here:  http://www.aviationweather.gov/adds/data/satellite/

//$filename1='http://www.aviationweather.gov/adds/data/satellite/latest_PIR_vis.jpg';
//$filename2='http://www.aviationweather.gov/adds/data/satellite/latest_AMA_vis.jpg';
//$filename3='http://www.aviationweather.gov/adds/data/satellite/latest_LIT_vis.jpg';
//$filename4='http://www.aviationweather.gov/adds/data/satellite/latest_MSP_vis.jpg';
//$filename5='http://www.aviationweather.gov/adds/data/satellite/latest_ICT_vis.jpg';

$filename1='https://aviationweather.gov/data/obs/sat/us/sat_vis_pir.jpg';
$filename2='https://aviationweather.gov/data/obs/sat/us/sat_vis_abq.jpg';
$filename3='https://aviationweather.gov/data/obs/sat/us/sat_vis_lit.jpg';
$filename4='https://aviationweather.gov/data/obs/sat/us/sat_vis_msp.jpg';
$filename5='https://aviationweather.gov/data/obs/sat/us/sat_vis_ict.jpg';

$fullfile = './centralplainsvisible.jpg'; //the name of the file that is created.
$maxage = 600; //the maximum age of the file in seconds.

/*
$myFile = "timestamp.txt";
$fh = fopen($myFile, 'r');
$oldstamp = fread($fh, 10);
fclose($fh);
$fh = fopen($myFile, 'w') or die("can't open file");
fwrite($fh, filemtime_remote($filename5));
*/
$timestamp = date('Ymd-His');


if (time()-filemtime($fullfile)>$maxage)
	{
	    $crop = imagecreatetruecolor(1064,1160);
	    $image = imagecreatefromjpeg($filename1);
	    imagecopy ( $crop, $image, 64, 0, 0, 37, 680, 643 );
	    $image = imagecreatefromjpeg($filename3);
	    imagecopy ( $crop, $image, 404, 580, 0, 37, 680, 643 );
	    $image = imagecreatefromjpeg($filename4);
	    imagecopy ( $crop, $image, 404, 0, 0, 37, 680, 643 );
	    $image = imagecreatefromjpeg($filename5);
	    imagecopy ( $crop, $image, 205, 420, 0, 37, 670, 643 );
        $image = imagecreatefromjpeg($filename2);
        imagecopy ( $crop, $image, 0, 561, 236, 37, 444, 643 );
	    $crop2 = imagecreatetruecolor(1000,1150);
	    imagecopy ( $crop2, $crop, 0, 0, 64, 10, 1000, 1150 );
	    imagejpeg($crop2,$fullfile,80);
	}

if ($_GET[batch] == "image")
	{
	    header('Content-type: image/jpeg');
	    $image = imagecreatefromjpeg($fullfile);
	    imagejpeg($image);
	}
elseif ($_GET[batch] == "attachment")
	{
    	    header('Content-type: image/jpeg');
	    header('Content-Disposition: attachment; filename='.$fullfile);
	    $image = imagecreatefromjpeg($fullfile);
	    imagejpeg($image);
	}
elseif ($_GET[batch] != "yes")
	{
	 echo "<html> \n";
        echo "<head> \n";
	 echo "  <title>Central Plains Visible Satellite</title> \n";
	 echo "</head> \n";
	 echo "<body> \n";
	 if (file_exists($fullfile)) 
	    {
	        $difftime = time()-filemtime($fullfile);
	        echo "Image age: " . $difftime ." seconds (max " . $maxage . ")<br /> \n";
	    }
	 echo $oldstamp . "<br /> \n";
	 echo "<img src=\"" . $fullfile . "?nocache=" . $timestamp . "\"> \n";
	 echo "<br />Options for building the image only (without the text/html): \n";
        echo "<br /><a href=\"centralplainsvisible.php?batch=yes\">Build File only! No output</a> \n";
        echo "<br /><a href=\"centralplainsvisible.php?batch=image\">return image only. no other display.</a> \n";
        echo "<br /><a href=\"centralplainsvisible.php?batch=attachment\">return image as an attachment/downloadable file. no other display.</a> \n";
	 echo "<br />If you want to load a place file for GR3, use the following text file. <a href=\"./centralplainsvisible.txt\">centralplainsvisible.txt</a> \n";
	 echo "</body> \n";
	 echo "</html> \n";
    }

fclose($fh);


function filemtime_remote($uri)
 {
     $uri = parse_url($uri);
     $handle = @fsockopen($uri['host'],80);
     if(!$handle)
         return 0;
 
    fputs($handle,"GET $uri[path] HTTP/1.1\r\nHost: $uri[host]\r\n\r\n");
     $result = 0;
     while(!feof($handle))
     {
         $line = fgets($handle,1024);
         if(!trim($line))
             break;
 
        $col = strpos($line,':');
         if($col !== false)
         {
             $header = trim(substr($line,0,$col));
             $value = trim(substr($line,$col+1));
             if(strtolower($header) == 'last-modified')
             {
                 $result = strtotime($value);
                 break;
             }
         }
     }
     fclose($handle);
     return $result;
 }
  
?>