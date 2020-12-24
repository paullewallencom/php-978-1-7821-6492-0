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
//get the edismax component.
$query->setQuery('cat:book');
//set fields to fetch
$query->setFields(array('id','name','price','author','score','last_modified'));
//set number of rows to fetch
$query->setRows(25);
//filter query to show books only in stock
$fquery = $query->createFilterQuery('inStock');
$fquery->setQuery('inStock:true');
$fquery->addTag('inStockTag');
//get the facetset component
$facetset = $query->getFacetSet();
//create a facet query to count books of genre fantasy & fiction
$facetmqry = $facetset->createFacetMultiQuery('genre');
$facetmqry->createQuery('genre_fantasy','genre_s: fantasy');
$facetmqry->createQuery('genre_fiction','genre_s: fiction');
//exclude the filter query during faceting
$facetmqry->addExclude('inStockTag');
//execute the query
$resultSet = $client->select($query);
//number of results found
$found = $resultSet->getNumFound();
echo "Total Results Found : $found <br/>".PHP_EOL;
//display facet count for fantasy & fiction books
echo "MultiQuery facet counts<br/>".PHP_EOL;
$facetCnts = $resultSet->getFacetSet()->getFacet('genre');
foreach($facetCnts as $fct => $cnt)
{
	echo $fct.': ['.$cnt.']'."<br/>".PHP_EOL;
}
echo PHP_EOL."Results:".PHP_EOL;
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
?>
