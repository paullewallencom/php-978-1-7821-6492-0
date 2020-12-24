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

//create a select query
$query = $client->createSelect();
//search for all books
$query->setQuery('cat:book AND author:Martin');
//$query->setQuery('author:Martin');
//start from 2 and search for next 5 results.
$query->setStart(2)->setRows(5);
//set the fields to return as result.
$query->setFields(array('id','name','price','author','score'));
//sort on price in ascending order
$query->addSorts(array('price' => 'asc','score' => 'desc'));
//get query details
//$stats = $resultSet->getStats();
//var_dump($stats);
//fire the query and get the result
$resultSet = $client->select($query);
//number of results found
$found = $resultSet->getNumFound();
echo "Results Found : $found<br/>".PHP_EOL;
//Show documents in the resultset.
//iterate through all documents in a result.
foreach($resultSet as $doc)
{
	echo PHP_EOL."-------".PHP_EOL;
	echo PHP_EOL."ID : ".$doc->id;
	echo PHP_EOL."Name : ".$doc->name;
	echo PHP_EOL."Author : ".$doc->author;
	echo PHP_EOL."Price : ".$doc->price;
	echo PHP_EOL."Score : ".$doc->score;
}

?>
