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
$daten['Error'] = $parser->getError();
$daten['Request'] = $parser->getRequests();
$daten['Time'] = $parser->getTime();
$daten['Type'] = $parser->getType();

print(json_encode($daten));