<?php
// Placefile Reference http://www.grlevelx.com/manuals/gis/files_places.htm
require('./WeatherFunctions.php');
header('Content-Type: text/plain');
$BaseUrl = 'https://www.spc.noaa.gov';
$day1outlookurl = "https://www.spc.noaa.gov/products/outlook/day1otlk.html";

$readday1 = file_get_contents($day1outlookurl);

$getday1texturl = '/\/products\/outlook\/archive\/[0-9]{4}\/.{10}_[0-9]{12}\.txt/';
preg_match_all($getday1texturl, $readday1, $Day1TextLinkMatch);
$Day1TextLink = $BaseUrl . $Day1TextLinkMatch[0][0];

$readday1Text = file_get_contents($Day1TextLink);

$ValidTime = '/VALID TIME [0-9]{6}Z - [0-9]{6}Z/';
preg_match_all($ValidTime, $readday1Text, $Day1TextValidTimeMatch);
$Day1TextValidTime = $Day1TextValidTimeMatch[0][0];
$PlacefileText = "Refresh: 20
Threshold: 999
Title: WS - Day 1 Wind Outlook - $Day1TextValidTime
Color: 222 62 9
";
$readday1TextArray = explode("\n", $readday1Text);

#Get Wind.
$capturing = 0;
foreach ($readday1TextArray as $Day1Line) {
    $WindStartTextMatch = '/\.\.\. WIND \.\.\./';
    $StartMatches = preg_match_all($WindStartTextMatch, $Day1Line, $Text);
    $WindEndTextMatch = '/&&/';
    $EndMatches = preg_match_all($WindEndTextMatch, $Day1Line, $Text);
    If ($EndMatches == 1 && $capturing == 1){
        $capturing = 0;
        break;
    }
    If ($capturing == 1){
        $WindPlainText[] = $Day1Line;
    }
    If ($StartMatches == 1 && $capturing == 0){
        $capturing = 1;
    }
}

$Winds = array();
$WindProb = array();
$WindCount = -1;
foreach ($WindPlainText as $WindLine) {
    $WindStartTextMatch = '/([0-9\.SIGN]{4})   /';
    $StartMatches = preg_match_all($WindStartTextMatch, $WindLine, $Text);
    If ($StartMatches == 1){
        $capturing = 1;
        $StartMatches = 0;
        $WindCount++;
        $WindProb[$WindCount] = $Text[1][0];
    }
    if ($capturing == 1){
        $CoordMatch = '/ ([0-9]{8})/';
        preg_match_all($CoordMatch, $WindLine, $AllMatches);
        foreach ($AllMatches[1] as $Coord){
            $Winds[$WindCount][] = $Coord;
        }
    }
}

for ($i=0; $i <= $WindCount; $i++) {
    if ($WindProb[$i] != 'SIGN'){
        $ProbabilityRing = 'Wind ' . ($WindProb[$i] * 100) . '%';
        $RGB = prob_color('Wind', $WindProb[$i]);
        $OutlookArea = PF_LineBox($ProbabilityRing, $Winds[$i], $RGB);
    } else {
        $ProbabilityRing = 'Significant Wind';
        $RGB = prob_color('Wind', $WindProb[$i]);
        $RGBA = $RGB . " 90";
        $RGBA = str_replace(" ", ",", $RGBA);
        $OutlookArea = PF_Polygon($ProbabilityRing, $Winds[$i], $RGBA);
        $PlacefileText = "$PlacefileText\n$OutlookArea\n";
        $OutlookArea = PF_LineBox($ProbabilityRing, $Winds[$i], $RGB);
    }
    $PlacefileText = "$PlacefileText\n$OutlookArea\n";
}

print $PlacefileText;


?>