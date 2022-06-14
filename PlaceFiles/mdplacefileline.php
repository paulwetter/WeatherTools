<?php
// Placefile Reference http://www.grlevelx.com/manuals/gis/files_places.htm
require('./WeatherFunctions.php');
header('Content-Type: text/plain');
$BaseUrl = 'https://www.spc.noaa.gov';
$mcdurl = "https://www.spc.noaa.gov/products/md/";

$PlacefileText = "Refresh: 10
Threshold: 999
Title: WS - SPC Mesoscale Discussions
Color: 229 222 229
";

$readmcd = file_get_contents($mcdurl);

$getmcdnum = '/<strong><a href=\"(\/products\/md\/md[0-9]{4}\.html)\">(Mesoscale Discussion #([0-9]{1,4}))<\/a><\/strong>/';
preg_match_all($getmcdnum, $readmcd, $MDMatches);

//$MDMatches[1][0] = '/products/md/md1115.html';

foreach ($MDMatches[1] as $MD) {
    $MDUrl = "$BaseUrl$MD";
    $ReadMD = file_get_contents($MDUrl);
    $MdNameMatch = '/<title>Storm Prediction Center (Mesoscale Discussion [0-9]{1,4})<\/title>/';
    preg_match_all($MdNameMatch, $ReadMD, $MDNameMatches);
    $MDName = $MDNameMatches[1][0];
    $LatLonMatch = '/ ([0-9]{8})/';
    preg_match_all($LatLonMatch, $ReadMD, $MDLatLonMatches);
    $GPS = $MDLatLonMatches[1];
    $MDLine = PF_LineBox($MDName,$GPS, '229 222 229');
    $PlacefileText = "$PlacefileText\n$MDLine\n";

    /* Future code to attach text for MD.
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
    */
}
print($PlacefileText);
?>