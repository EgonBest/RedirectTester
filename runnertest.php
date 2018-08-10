<?php
include "hybrid/Runner.php";
$debug = false;
$ret = [];
$ret['Error'] = false;
$ret['Size'] = 0;
$ret['Requests'] = 0;
$ret['Time'] = 0;
$ret['Type'] = [];
$ret['Daten'] = [];
error_reporting('E_ERROR');
$runner = new \HybridLogic\PhantomJS\Runner();
$result = $runner->execute("phantomjsScripts/netsniff.js",htmlentities($_POST['testUrl']));
if($debug == true){
    error_reporting('E_ALL');
    echo "<pre>";
    print_r($result);
    echo "</pre>";
}
if(!is_array($result)){
    $ret['Error'] = true;
    if($debug == true) {
        echo "<h2>Scheinbar stimmt das HTTPS Protokoll nicht</h2>";
    }
} else {
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
        $Type[$TypeKey] +=1;
        $allSize = $allSize + $size;
        if($debug == true) {
            echo $type[0];
            echo $size;
            echo " Byte <br>";
        }
    }
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