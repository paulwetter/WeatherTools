<?php
// Placefile Reference http://www.grlevelx.com/manuals/gis/files_places.htm
require('./WeatherFunctions.php');
header('Content-Type: text/plain');
$BaseUrl = 'https://www.spc.noaa.gov';
$wwurl = "https://www.spc.noaa.gov/products/watch/";

$PlacefileText = "Refresh: 10
Threshold: 999
Title: WS - SPC Watch Boxes (Polygon)
";

$readww = file_get_contents($wwurl);

$getwwnum = '/<strong><a href=\"(\/products\/watch\/ww[0-9]{4}\.html)\">(.+Watch #([0-9]{1,4}))<\/a><\/strong>/';
preg_match_all($getwwnum, $readww, $WWMatches);
//$WWMatches[1][0] = '/products/watch/ww0344.html';
foreach ($WWMatches[1] as $WW) {
    $WWUrl = "$BaseUrl$WW";
    $ReadWW = file_get_contents($WWUrl);
    $WatchNameMatch = '/<title>Storm Prediction Center +(.+ Watch [0-9]{1,4})<\/title>/';
    preg_match_all($WatchNameMatch, $ReadWW, $WatchNameMatches);
    $WatchName = $WatchNameMatches[1][0];
    $LatLonMatch = '/ ([0-9]{8})/';
    preg_match_all($LatLonMatch, $ReadWW, $WWLatLonMatches);
    $GPS = $WWLatLonMatches[1];
    $RGB = '9,87,222,50';
    if (strpos($WatchName, 'Tornado') !== false){
        $RGB = '255,0,0,50';
    }
    $Polygon = PF_Polygon($WatchName,$GPS,$RGB);
    $PlacefileText = "$PlacefileText\n$Polygon\n";

/* Future code to attach text for watch.
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
*/
}
print($PlacefileText);
?>