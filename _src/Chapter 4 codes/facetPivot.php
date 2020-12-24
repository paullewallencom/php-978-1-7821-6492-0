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
$query->setRows(5);
//get the facetset component
$facetset = $query->getFacetSet();
//create a range facet with respect to price.
$facetqry = $facetset->createFacetPivot('genre-instock');
$facetqry->addFields('genre_s,inStock');
$facetqry->setMinCount(0);
//execute the query
$resultSet = $client->select($query);
//number of results found
$found = $resultSet->getNumFound();
echo "Total Results Found : $found <br/>".PHP_EOL;
//display facet count for fantasy & fiction books
echo "Pivot facets : <br/>".PHP_EOL;
$facetResult = $resultSet->getFacetSet()->getFacet('genre-instock');
foreach($facetResult as $pivot)
{
	displayPivot($pivot);
}

function displayPivot($pivot)
{
	echo "<ul>".PHP_EOL;
	echo '<li>Field: '.$pivot->getField()."</li>".PHP_EOL;
	echo '<li>Value: '.$pivot->getValue()."</li>".PHP_EOL;
	echo '<li>Count: '.$pivot->getCount()."</li>".PHP_EOL;
	foreach($pivot->getPivot() as $nextPivot)
		displayPivot($nextPivot);
	echo "</ul>".PHP_EOL;
}
?>
