<?php
/**
 * Created by PhpStorm.
 * User: Best
 * Date: 16.08.2018
 * Time: 08:22
 */

namespace RedirectTester;
include "hybrid/Runner.php";
error_reporting(0);

class parseJson
{
    /**
     * @var
     */
    private $error;
    /**
     * @var
     */
    private $size;
    /**
     * @var
     */
    private $requests;
    /**
     * @var
     */
    private $time;
    /**
     * @var
     */
    private $type;
    /**
     * @var
     */
    private $daten;

    /**
     * @var
     */
    private $debug;
    /**
     * @var
     */
    private $script;
    /**
     * @var
     */
    private $url;
    /**
     * @var
     */
    private $result;

    /**
     * parseJson constructor.
     * @param $url
     */
    public function __construct($url)
    {
        $this->debug = false;
        $this->error = false;
        $this->size = 0;
        $this->requests = 0;
        $this->time = 0;
        $this->type = [];
        $this->daten = "";
        $this->script = "phantomjsScripts/netsniff.js";
        $this->setUrl($url);
        $this->callData();
        $this->parseData();
    }

    /**
     * @return bool
     */
    private function callData()
    {
        $runner = new \HybridLogic\PhantomJS\Runner();
        $this->result = $runner->execute($this->script, $this->url);
        if(!is_array($this->result)){
            $this->error = true;
            $pattern = "/^{.*^}/ms";
            preg_match_all($pattern, $this->result, $matches);
            $this->result = json_decode($matches[0][0],true);
        }
        return $this->error;
    }

    /**
     *
     */
    private function parseData()
    {
        $allSize = 0;
        $allRequest = 0;
        $ladezeit = $this->result['log']['pages'][0]['pageTimings']['onLoad'];
        $Type = [];
        foreach ($this->result["log"]["entries"] as $entrie) {
            $allRequest++;
            $size = $entrie["response"]["content"]["size"];
            $type = explode("/", $entrie["response"]["content"]["mimeType"]);
            $type = explode(";", $type[1]);
            $TypeKey = ($type[0]!=="")?$type[0]:"ohne";
            $Type[$TypeKey]['Requests'] +=1;
            $Type[$TypeKey]['Size'] = ($size > 0)?$Type[$TypeKey]['Size'] + $size:$Type[$TypeKey]['Size'];
            $allSize = $allSize + $size;
            $arr['url'] = htmlentities($entrie["request"]["url"]);
            $arr['size'] = $size;
            $arr['time'] = $entrie["time"];
            if(is_array($Type[$TypeKey]['Urls'])){
                array_push($Type[$TypeKey]['Urls'],$arr);
            } else {
                $Type[$TypeKey]['Urls']=[];
                $Type[$TypeKey]['Urls'][0]=$arr;
            }
            $allSize = ($size > 0)?$allSize + $size:$allSize;
        }
        $this->size = $allSize;
        $this->requests = $allRequest;
        $this->time = $ladezeit;
        $this->type = $Type;
        $this->daten = $this->result;
        if($this->debug===true){
            $file = fopen('log/error.log', 'w');
            fwrite($file, $this->result);
            fclose($file);
        }
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->daten;
    }

    /**
     * @return int
     */
    public function getSize(){
        return $this->size;
    }

    /**
     * @return int
     */
    public function getRequests(){
        return $this->requests;
    }

    /**
     * @return array
     */
    public function getType(){
        return $this->type;
    }

    /**
     * @return int
     */
    public function getTime(){
        return $this->time;
    }

    /**
     * @return bool
     */
    public function getError(){
        return $this->error;
    }
    /**
     * @param $script
     */
    public function setScript($script)
    {
        $this->script = $script;
    }

    /**
     * @param $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @param $debug
     */
    public function setDebug($debug)
    {
        $this->debug = $debug;
    }


}