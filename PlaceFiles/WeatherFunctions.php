<?php

function left($str, $length) {
    return substr($str, 0, $length);
}

function right($str, $length) {
    return substr($str, -$length);
}

function LatLon(String $oldnum) {
    $Lat = Left($oldnum,4);
    $Lon = Right($oldnum,4);
    $Lat = Left($Lat,2) . "." . Right($Lat,2);
    $Lon = Left($Lon,2) . "." . Right($Lon,2);
    If ($Lon < 30.00){
        $Lon = "-1" . $Lon;
    } else {
        $Lon = "-" . $Lon;
    }
    return $Lat . ", " . $Lon ;
    }

function PF_LineBox(String $Title, Array $Coords, String $RGB) {
    $PlacefileText = ";$Title
Color: $RGB
Line: 5, 0, $Title\n";
    foreach ($Coords as $CoOrd) {
        $PointLocation = LatLon($CoOrd);
        $PlacefileText = "$PlacefileText$PointLocation\n";
    }
    $PlacefileText = $PlacefileText . "End:\n";
return $PlacefileText;
}


function PF_Polygon(String $Title, Array $Coords, String $RGBA){
    $Polygon = ";$Title
Polygon:\n";
    $firstcoord = 1;
    foreach ($Coords as $CoOrd) {
        $PointLocation = LatLon($CoOrd);
        if ($firstcoord == 1){
            $firstcoord = 0;
            #$RGBA = 255,0,0,30
            $Polygon = "$Polygon$PointLocation,$RGBA\n";
        } else {
            $Polygon = "$Polygon$PointLocation\n";
        }
    }
    $Polygon = $Polygon . "End:\n";
return $Polygon;
}


function prob_color(string $Type, string $Percent) {
    switch ($Type) {
        case 'Tor':
            switch ($Percent) {
                case '0.02':
                    return '48 212 63';
                case '0.05':
                    return '254 255 14';
                case '0.10':
                    return '255 148 0';
                case '0.15':
                    return '255 10 11';
                case '0.30':
                    return '254 1 255';
                case '0.45':
                    return '254 1 255';
                case '0.60':
                    return '254 1 255';
                default:
                    return '255 255 255';
            }
        case 'Wind':
            switch ($Percent) {
                case '0.05':
                    return '139 71 38';
                case '0.15':
                    return '255 200 0';
                case '0.30':
                    return '255 0 0';
                case '0.45':
                    return '255 0 255';
                case '0.60':
                    return '145 44 238';
                case 'SIGN':
                    return '100 100 100';
                default:
                    return '255 255 255';
            }
        case 'Hail':
            switch ($Percent) {
                case '0.05':
                    return '139 71 38';
                case '0.15':
                    return '255 200 0';
                case '0.30':
                    return '255 0 0';
                case '0.45':
                    return '255 0 255';
                case '0.60':
                    return '145 44 238';
                case 'SIGN':
                    return '100 100 100';
                default:
                    return '255 255 255';
            }
                        
        default:
            # code...
            break;
    }

}
?>