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
//http://localhost:8080/solr/collection1/select/?q=smith
$updateQuery = $client->createUpdate();
//Add a test document
$testdoc = $updateQuery->createDocument();
$testdoc->id = 123456789;
$testdoc->cat = 'book';
$testdoc->name = 'Test book';
$testdoc->price = 5.99;
$testdoc->author = 'Hello Smith';

$updateQuery->addDocument($testdoc);
$updateQuery->addCommit();
$client->update($updateQuery);

//http://localhost:8080/solr/collection1/select/?q=smith
//update the name of the author to 'Jack Smith'

$updateQuery2 = $client->createUpdate();

$testdoc2 = $updateQuery2->createDocument();
$testdoc2->id = 123456789;
$testdoc2->cat = 'book';
$testdoc2->name = 'Test book';
$testdoc2->price = 7.59;
$testdoc2->author = 'Jack Smith';

$updateQuery2->addDocument($testdoc2, true);
$updateQuery2->addCommit();
$client->update($updateQuery2);

//http://localhost:8080/solr/collection1/select/?q=smith
//Delete this document
$deleteQuery = $client->createUpdate();
$deleteQuery->addDeleteQuery('author:Smith');
$deleteQuery->addCommit();
$client->update($deleteQuery);
//http://localhost:8080/solr/collection1/select/?q=smith

?>