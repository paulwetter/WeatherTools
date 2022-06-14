<?php
// Placefile Reference http://www.grlevelx.com/manuals/gis/files_places.htm
require('./WeatherFunctions.php');
header('Content-Type: text/plain');
$BaseUrl = 'https://www.spc.noaa.gov';
//$mcdurl = "testmcd.txt";
$wwurl = "https://www.spc.noaa.gov/products/watch/";

$PlacefileText = "Refresh: 10
Threshold: 999
Title: SPC Polygon Watch Boxes
Color: 255 0 0
";

$readww = file_get_contents($wwurl);
//echo $readww;


$getwwnum = '/<strong><a href=\"(\/products\/watch\/ww[0-9]{4}\.html)\">(.+Watch #([0-9]{1,4}))<\/a><\/strong>/';
preg_match_all($getwwnum, $readww, $WWMatches);
//print_r($MDMatches[1]);

 
foreach ($WWMatches[1] as $WW) {
    $WWUrl = "$BaseUrl$WW";
    $ReadWW = file_get_contents($WWUrl);
    //print($ReadWW);
    $LatLonMatch = '/ ([0-9]{8})/';
    preg_match_all($LatLonMatch, $ReadWW, $WWLatLonMatches);
    $GPS = $WWLatLonMatches[1];
    //print_r($GPS);
    $PlacefileText = "$PlacefileText 
;SV Watch Number $WW
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
    $WWFileArray = explode("\n", $ReadWW);
    $capturing = 0;
    foreach ($WWFileArray as $WWFileLine) {
        $WWStartTextMatch = '/<pre>/';
        $StartMatches = preg_match_all($WWStartTextMatch, $WWFileLine, $Text);
        $WWEndTextMatch = '/<\/pre>/';
        $EndMatches = preg_match_all($WWEndTextMatch, $WWFileLine, $Text);
        If ($EndMatches == 1 && $capturing == 1){
            $capturing = 0;
            break;
        }
        If ($capturing == 1){
            $WWPlainText[] = $WWFileLine;
        }
        If ($StartMatches == 1 && $capturing == 0){
            $capturing = 1;
        }
    }
    //print_r($WWPlainText);
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