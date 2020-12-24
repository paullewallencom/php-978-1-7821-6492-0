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
//Delete this document
$deleteQuery = $client->createUpdate();
$deleteQuery->addDeleteById('123456789');
$deleteQuery->addCommit();
$client->update($deleteQuery);
//http://localhost:8080/solr/collection1/select/?q=smith

?>
