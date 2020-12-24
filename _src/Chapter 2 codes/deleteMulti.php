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
$deleteQuery = $client->createUpdate();
//http://localhost:8080/solr/collection1/select/?q=author:Brust
//http://localhost:8080/solr/collection1/select/?q=author:Alexander
$deleteQuery->addDeleteQueries(array('author:Burst','author:Alexander'));
$deleteQuery->addCommit();
$result = $client->update($deleteQuery);

echo 'Status : '.$result->getStatus()."<BR/>".PHP_EOL;
var_dump($result->getData());
?>