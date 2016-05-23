<?php
/**
 * Created by IntelliJ IDEA.
 * User: op49265
 * Date: 17.03.2016
 * Time: 12:07
 */

include ("freifunkStuff/class/Data.php");
include_once ("freifunkStuff/class/Node.php");
include ("pointLocation.php");

set_time_limit ( 1000 );

$areaCounts = array();
$folder = "areas";
$data = array();
if ($handle = opendir($folder)) {
    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != "..") {
            $path = $folder."/".$entry;
            $data[] = file_get_contents($path);
        }
    }
    closedir($handle);
}
$areas = json_decode("[".implode(",",$data)."]",true);
usort($areas, function($a, $b) {
    return $b['id'] - $a['id'];
});

$dataURLs = array("http://map.ffnw.de/public/data/batman-v15/nodes.json","http://mesh.ff-osna.de/data/nodes.json");
$clientData = array();
foreach($dataURLs as $url){
    $clients = json_decode(Data::get_remote_data($url),true);
    foreach($clients['nodes'] as $nodeData){
        $node = new Node();
        $node->setRawData(json_encode($nodeData));
        $node->parseRawData();
        $areaCode = getAreaForNode($node,$areas);
        if($areaCode){
            if(!isset($areaCounts[$areaCode])){
                $areaCounts[$areaCode] = 1;
            }
            else{
                $areaCounts[$areaCode]++;
            }
        }
    }
}

echo json_encode($areaCounts);
$fd = fopen(__DIR__."/data.json","w");
fwrite($fd,json_encode($areaCounts));
fclose($fd);

/**
 * @param Node $node
 * @param $areas
 */
function getAreaForNode($node, $areas){
    $pointLocation = new pointLocation();
    foreach($areas as $area){
        $areaPoints = array();
        foreach($area["geometry"]["coordinates"][0][0] as $areaPoint){
            $areaPoints[] = $areaPoint[1]." ".$areaPoint[0];
        }
        $point = $node->getNodeinfo()->getLocation()->getLatitude()." ".$node->getNodeinfo()->getLocation()->getLongnitude();
        if($pointLocation->pointInPolygon($point,$areaPoints)){
            return $area["id"];
        }
    }
    return false;
}



/**
 * @return string
 */
function getBaseUrl()
{
    // output: /myproject/index.php
    $currentPath = $_SERVER['PHP_SELF'];

    // output: Array ( [dirname] => /myproject [basename] => index.php [extension] => php [filename] => index )
    $pathInfo = pathinfo($currentPath);

    // output: localhost
    $hostName = $_SERVER['HTTP_HOST'];

    // output: http://
    $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5))=='https://'?'https://':'http://';

    // return: http://localhost/myproject/
    return $protocol.$hostName.$pathInfo['dirname']."/";
}
