<?php
include "hybrid/Runner.php";
$debug = true;
$ret = [];
$ret['Error'] = false;
$ret['Size'] = 0;
$ret['Requests'] = 0;
$ret['Time'] = 0;
$ret['Type'] = [];
$ret['Daten'] = [];
error_reporting('E_ERROR');
$runner = new \HybridLogic\PhantomJS\Runner();
$result = $runner->execute("phantomjsScripts/netsniff.js",htmlentities($_GET['testUrl']));
if($debug == true){
    error_reporting('E_ALL');
    echo "<pre>";
    print_r($result);
    echo "</pre>";
}
if(!is_array($result)){
    $ret['Error'] = true;
    if($debug == true) {
        echo "<h2>Scheinbar stimmt das HTTPS Protokoll nicht</h2>";#
    }
    $pattern = "/^{.*^}/ms";

    preg_match_all($pattern, $result, $matches);
    $result = json_decode($matches[0][0],true);
}
if(is_array($result)){
    $allSize = 0;
    $allRequest =0;
    $ladezeit = $result['log']['pages'][0]['pageTimings']['onLoad'];
    $Type = [];
    foreach( $result["log"]["entries"] as $entrie){
        $allRequest++;
        $size = $entrie["response"]["content"]["size"];
        $type = explode("/",$entrie["response"]["content"]["mimeType"]);
        $type = explode(";",$type[1]);
        $TypeKey = $type[0];
        $Type[$TypeKey]['Requests'] +=1;
        $Type[$TypeKey]['Size'] = ($size > 0)?$Type[$TypeKey]['Size'] + $size:$Type[$TypeKey]['Size'];
        $arr['url'] = htmlentities($entrie["request"]["url"]);
        $arr['size'] = $size;
        if(is_array($Type[$TypeKey]['Urls'])){
            array_push($Type[$TypeKey]['Urls'],$arr);
        } else {
            $Type[$TypeKey]['Urls'][0]=$arr;
        }
        $allSize = ($size > 0)?$allSize + $size:$allSize;
        if($debug == true) {
            echo $type[0];
            echo $size;
            echo " Byte <br>";
        }
    }
    echo "<pre>";
    print_r($Type);
    echo "</pre>";
    if($debug == true) {
        echo "Gesamtgröße " . $allSize;
        echo "<br>";
        echo "Requests " . $allRequest;
        echo "<br>";
        foreach ($Type as $key => $value) {
            echo $key . " = " . $value;
            echo "<br>";
        }
    }
    $ret['Size']=$allSize;
    $ret['Requests']=$allRequest;
    $ret['Time']=$ladezeit;
    $ret['Type']=$Type;
    $ret['Daten']=$result;
    print(json_encode($ret));
}