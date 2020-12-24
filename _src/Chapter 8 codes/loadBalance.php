<?php
include_once("vendor/autoload.php");

$config = array(
	"master" => array(
		"master" => array(
			"host"=>"127.0.0.1",
			"port"=>"8080",
			"path"=>"/solr",
			"core"=>"collection1",
		),		
	),
	"slave" => array(
		"slave" => array(
			"host"=>"127.0.0.1",
			"port"=>"8983",
			"path"=>"/solr",
			"core"=>"collection1",
		),
	)
);

$client = new Solarium\Client($config);

//create endpoints to add to the load balancer
$masterEndpoint = $client->createEndpoint("master");
$slaveEndpoint = $client->createEndpoint("slave");

//get the load balancer plugin
$lb = $client->getPlugin('loadbalancer');
//add endpoints with respective weights
$lb->addEndpoint($masterEndpoint, 1);
$lb->addEndpoint($slaveEndpoint, 5);

//get list of blocked queries
$blockedQry = $lb->getBlockedQueryTypes();
echo "Blocked Query types : <br/>".PHP_EOL;
print_r($blockedQry);

//enable failover if query fails on any server after 2 retries
$lb->setFailoverEnabled(true);
$lb->setFailoverMaxRetries(2);

//create a select query
$query = $client->createSelect();
//get the edismax component.
$query->setQuery('cat:book');
//set fields to fetch
$query->setFields(array('id','name','author','series_t','score','price'));

// execute the query multiple times, displaying the server for each execution
for($i=1; $i<=6; $i++) {
    $resultset = $client->select($query);
    echo 'Query execution #' . $i . "<br/>".PHP_EOL;
    echo 'Results Found: ' . $resultset->getNumFound(). "<br/>".PHP_EOL;
    echo 'Solr Server: ' . $lb->getLastEndpoint() ."<hr/>".PHP_EOL.PHP_EOL;
}

// force a server for a query 
$lb->setForcedEndpointForNextQuery('master');
$resultset = $client->select($query);
echo 'Results Found: ' . $resultset->getNumFound(). "<br/>".PHP_EOL;
echo 'Solr Server: ' . $lb->getLastEndpoint() ."<hr/>".PHP_EOL.PHP_EOL;

?>