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

$selectConfig = array(
	'query' => 'cat:book AND author:Martin',
	'start' => 2,
	'rows' => 5,
	'fields' => array('id','name','price','author'),
	'sort' => array('price' => 'asc'),		
);

$client = new Solarium\Client($config);

//create a select query
$query = $client->createSelect($selectConfig);

//check the query and sorting criteria
echo "Query : ".$query->getQuery().PHP_EOL;
echo "Sort : ";
var_dump($query->getSorts()).PHP_EOL;
echo "Omit Header : ".$query->getOmitHeader().PHP_EOL;
$query->setOmitHeader(false);
//fire the query and get the result
$resultSet = $client->select($query);
//number of results found
$found = $resultSet->getNumFound();
echo "Results Found : $found<br/>".PHP_EOL;
$stats = $query->getStats();
echo "Stats : ";
var_dump($stats).PHP_EOL;
//Show documents in the resultset.
//iterate through all documents in a result.
foreach($resultSet as $doc)
{
	echo PHP_EOL."-------".PHP_EOL;
	echo PHP_EOL."ID : ".$doc->id;
	echo PHP_EOL."Name : ".$doc->name;
	echo PHP_EOL."Author : ".$doc->author;
	echo PHP_EOL."Price : ".$doc->price;
}

?>