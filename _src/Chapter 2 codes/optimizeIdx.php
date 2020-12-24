<?php
include_once("vendor/autoload.php");

$config = array(
	"endpoint" => array(
		"localhost" => array(
			"host"=>"127.0.0.1",
			"port"=>"8080",
			"path"=>"/solr",
			"core"=>"collection1",
		),
	)
);

$client = new Solarium\Client($config);
//create instance of update query 
$optiQuery = $client->createUpdate();
$optiQuery->addOptimize($softcommit=true, $waitSearcher=false, $maxSegments=10);

$result = $client->update($optiQuery);
echo 'Status : '.$result->getStatus()."<br/>".PHP_EOL;

?>