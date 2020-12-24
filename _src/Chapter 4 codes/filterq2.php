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

$fqconfig = array(
		"query"=>"inStock:true",
		"key"=>"Availability",	
	);

$client = new Solarium\Client($config);

//create a select query
$query = $client->createSelect();
//get the edismax component.
$edismax = $query->getedismax();
//alternative query to run in case normal query is blank
$edismax->setQueryAlternative('*:*');
//set minimum number of clauses that should match
$edismax->setMinimumMatch('70%');
//set phrase query fields for proximity boosting
$edismax->setPhraseFields('series_t^5');
//set tie breaker
$edismax->setTie(0.1);
//set boost function 
$edismax->setBoostFunctionsMult('recip(ms(NOW,last_modified),1,1,1)');
//bigram search
$edismax->setPhraseBigramFields('name^2 author^1.8 series_t^1.3');
//search for all books except books by author martin in them
$query->setQuery('cat:book -author:martin');
//create filter on available (in stock) books
$query->addFilterQuery($fqconfig);
//set fields to fetch
$query->setFields(array('id','name','price','author','score','last_modified'));
//set number of rows to fetch
$query->setRows(25);
//fire the query and get the result
$resultSet = $client->select($query);
//number of results found
$found = $resultSet->getNumFound();
echo "Results Found : $found<br/>".PHP_EOL;
//Show documents in the resultset.
//iterate through all documents in a result.
foreach($resultSet as $doc)
{
	echo PHP_EOL."-------<br/>".PHP_EOL;
	echo PHP_EOL."ID : ".$doc->id;
	echo PHP_EOL."Name : ".$doc->name;
	echo PHP_EOL."Author : ".$doc->author;
	echo PHP_EOL."Price : ".$doc->price;
	echo PHP_EOL."Score : ".$doc->score;
	echo PHP_EOL."Last Modified : ".$doc->last_modified;
	echo "<br\>";
}
//display associated filter query
$fq = $query->getFilterQuery('Availability');
echo PHP_EOL.PHP_EOL."fq = ".$fq->getQuery().PHP_EOL;

?>