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
$updateQuery = $client->createUpdate();

//create the documents you want to add
$doc1 = $updateQuery->createDocument();
$doc1->id = 112233445;
$doc1->cat = 'book';
$doc1->name = 'A Feast For Crows';
$doc1->price = 8.99;
$doc1->inStock = 'true';
$doc1->author = 'George R.R. Martin';
$doc1->series_t = '"A Song of Ice and Fire"';
$doc1->sequence_i = 4;
$doc1->genre_s = 'fantasy';

//create the documents you want to add
$doc2 = $updateQuery->createDocument();
$doc2->id = 112233556;
$doc2->cat = 'book';
$doc2->name = 'A Dance with Dragons';
$doc2->price = 9.99;
$doc2->inStock = 'true';
$doc2->author = 'George R.R. Martin';
$doc2->series_t = '"A Song of Ice and Fire"';
$doc2->sequence_i = 5;
$doc2->genre_s = 'fantasy';

//add documents to the update query followed by commit statement
$updateQuery->addDocuments(array($doc1, $doc2), $overwrite=true);
$updateQuery->addCommit();

//execute the query
$result = $client->update($updateQuery);

//check the status of the query
echo "Status : ".$result->getStatus()."<br/>".PHP_EOL;
echo "time : ".$result->getQueryTime();

?>