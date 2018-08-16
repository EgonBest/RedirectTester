<?php
/**
 * Created by PhpStorm.
 * User: Best
 * Date: 16.08.2018
 * Time: 09:32
 */


include "parseJson.php";
$parser = new \RedirectTester\parseJson(htmlentities($_POST['testUrl']));
$daten = [];
$daten['data'] = $parser->getData();
$daten['request'] = $parser->getRequests();
$daten['type'] = $parser->getTime();
$daten['time'] = $parser->getType();

print(json_encode($daten));