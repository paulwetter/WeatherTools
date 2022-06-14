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
Title: WS - Day 1 Catagorical Outlook - $Day1TextValidTime
Color: 222 62 9
";
$readday1TextArray = explode("\n", $readday1Text);

#Get Catagorical.
$capturing = 0;
foreach ($readday1TextArray as $Day1Line) {
    $CatStartTextMatch = '/\.\.\. CATEGORICAL \.\.\./';
    $StartMatches = preg_match_all($CatStartTextMatch, $Day1Line, $Text);
    $CatEndTextMatch = '/&&/';
    $EndMatches = preg_match_all($CatEndTextMatch, $Day1Line, $Text);
    If ($EndMatches == 1 && $capturing == 1){
        $capturing = 0;
        break;
    }
    If ($capturing == 1){
        $CatPlainText[] = $Day1Line;
    }
    If ($StartMatches == 1 && $capturing == 0){
        $capturing = 1;
    }
}

$Cats = array();
$CatProb = array();
$CatCount = -1;
foreach ($CatPlainText as $CatLine) {
    $CatStartTextMatch = '/([MDTENHSLGTMRGLTSTM]{3,4})[ ]{3,4}/';
    $StartMatches = preg_match_all($CatStartTextMatch, $CatLine, $Text);
    If ($StartMatches == 1){
        $capturing = 1;
        $StartMatches = 0;
        $CatCount++;
        $CatProb[$CatCount] = $Text[1][0];
    }
    if ($capturing == 1){
        $CoordMatch = '/ ([0-9]{8})/';
        preg_match_all($CoordMatch, $CatLine, $AllMatches);
        foreach ($AllMatches[1] as $Coord){
            $Cats[$CatCount][] = $Coord;
        }
    }
}

for ($i=0; $i <= $CatCount; $i++) {
    switch ($CatProb[$i]) {
        case 'TSTM':
            $ProbabilityRing = 'Thunderstorm';
            $RGB = "192 232 192";
            break;
        case 'MRGL':
            $ProbabilityRing = 'Marginal';
            $RGB = "127 197 127";
            break;
        case 'SLGT':
            $ProbabilityRing = 'Slight';
            $RGB = "246 246 127";
            break;
        case 'ENH':
            $ProbabilityRing = 'Enhanced';
            $RGB = "230 194 127";
            break;
        case 'MDT':
            $ProbabilityRing = 'Moderate';
            $RGB = "230 127 127";
            break;
        case 'HIGH':
            $ProbabilityRing = 'High';
            $RGB = "255 127 255";
            break;
        default:
            $RGB = "255 255 255";
            break;
    }
    //$RGBA = $RGB . " 90";
    //$RGBA = str_replace(" ", ",", $RGBA);
    //$OutlookArea = PF_Polygon($ProbabilityRing, $Cats[$i], $RGBA);
    //$PlacefileText = "$PlacefileText\n$OutlookArea\n";
    $OutlookArea = PF_LineBox($ProbabilityRing, $Cats[$i], $RGB);
    $PlacefileText = "$PlacefileText\n$OutlookArea\n";
}

print $PlacefileText;


?>