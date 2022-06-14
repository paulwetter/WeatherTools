<?php
header('Content-Type: text/plain');
$MDUrl = 'https://www.spc.noaa.gov/products/md/md1045.html';
$ReadMD = file_get_contents($MDUrl);
//print($ReadMD);
$LatLonMatch = '/[0-9]{8}/';
preg_match_all($LatLonMatch, $ReadMD, $MDLatLonMatches);
$GPS = $MDLatLonMatches[0];
print_r($GPS);
$MDFileArray = explode("\n", $ReadMD);
$capturing = 0;
foreach ($MDFileArray as $MdFileLine) {
    $MDStartTextMatch = '/<pre>/';
    $StartMatches = preg_match_all($MDStartTextMatch, $MdFileLine, $Text);
    $MDEndTextMatch = '/<\/pre>/';
    $EndMatches = preg_match_all($MDEndTextMatch, $MdFileLine, $Text);
    If ($EndMatches == 1 && $capturing == 1){
        $capturing = 0;
    }
    If ($capturing == 1){
        $MDPlainText[] = $MdFileLine;
    }
    If ($StartMatches == 1 && $capturing == 0){
        $capturing = 1;
    }
}
print_r($MDPlainText);
?>