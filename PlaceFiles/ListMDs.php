<?php
// Placefile Reference http://www.grlevelx.com/manuals/gis/files_places.htm
require('./WeatherFunctions.php');
header('Content-Type: text/plain');
$BaseUrl = 'https://www.spc.noaa.gov';
//$mcdurl = "testmcd.txt";
$mcdurl = "https://www.spc.noaa.gov/products/md/";

$PlacefileText = "Refresh: 10
Threshold: 999
Title: SPC Mesoscale Discussions
Color: 215 222 9
";

$readmcd = file_get_contents($mcdurl);
//echo $readmcd;


$getmcdnum = '/<strong><a href=\"(\/products\/md\/md[0-9]{4}\.html)\">(Mesoscale Discussion #([0-9]{1,4}))<\/a><\/strong>/';
preg_match_all($getmcdnum, $readmcd, $MDMatches);
//print_r($MDMatches[1]);

 
foreach ($MDMatches[1] as $MD) {
    $MDUrl = "$BaseUrl$MD";
    $ReadMD = file_get_contents($MDUrl);
    //print($ReadMD);
    $LatLonMatch = '/ ([0-9]{8})/';
    preg_match_all($LatLonMatch, $ReadMD, $MDLatLonMatches);
    $GPS = $MDLatLonMatches[1];
    //print_r($GPS);
    $PlacefileText = "$PlacefileText 
;MD Number $MD
Line: 5, 0
";
    $firstcoord = 1;
    foreach ($GPS as $CoOrd) {
        $PointLocation = LatLon($CoOrd);
        if ($firstcoord == 1){
            $firstcoord = 0;
            $LocationOne = $PointLocation;
            $PlacefileText = "$PlacefileText$PointLocation
";
        } else {
            $PlacefileText = "$PlacefileText$PointLocation
";
        }
    }
    $PlacefileText = "$PlacefileText$LocationOne
";
    $MDFileArray = explode("\n", $ReadMD);
    $capturing = 0;
    foreach ($MDFileArray as $MdFileLine) {
        $MDStartTextMatch = '/<pre>/';
        $StartMatches = preg_match_all($MDStartTextMatch, $MdFileLine, $Text);
        $MDEndTextMatch = '/<\/pre>/';
        $EndMatches = preg_match_all($MDEndTextMatch, $MdFileLine, $Text);
        If ($EndMatches == 1 && $capturing == 1){
            $capturing = 0;
            break;
        }
        If ($capturing == 1){
            $MDPlainText[] = $MdFileLine;
        }
        If ($StartMatches == 1 && $capturing == 0){
            $capturing = 1;
        }
    }
    //print_r($MDPlainText);
}
$PlacefileText = $PlacefileText . "End:
";
print($PlacefileText);
/*
$wwUrl = "https://www.spc.noaa.gov/products/watch/";
$readww = file_get_contents($wwUrl);
$GetWWRegex = '/<strong><a href="(\/products\/watch\/ww[0-9]{4}.html)">(Severe Thunderstorm Watch #[0-9]{1,4})<\/a><\/strong>/';
preg_match_all($GetWWRegex, $readww, $wwmatches);
print_r($wwmatches);

foreach ($wwmatches as $ww) {
    echo "Watch $($ww[1])";
} 

$GetTorRegex = '/<strong><a href="(\/products\/watch\/ww[0-9]{4}.html)">(Tornado Watch #[0-9]{1,4})<\/a><\/strong>/';
preg_match_all($GetTorRegex, $readww, $Tormatches);
print_r($Tormatches);
*/
?>