<?php
include_once("vendor/autoload.php");
echo "Solarium Version : ".Solarium\Client::VERSION.PHP_EOL;

$config = array(
	"endpoint" => array(
		"localhost" => array(
			"host"=>"127.0.0.1",
			"port"=>"8080",
			"path"=>"/solr",
			"core"=>"collection1",
		)
	)
);

$client = new Solarium\Client($config);

$ping = $client->createPing();
try
{
	$result = $client->ping($ping);
	var_dump($result->getData());
	echo "Status : ".$result->getStatus().PHP_EOL;
}catch(Solarium\Exception $e)
{
	echo "ping failed".PHP_EOL;
}
?>