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
$facetqry = $facetset->createFacetQuery('genreFantasy');
$facetqry->setQuery('genre_s: fantasy');
//exclude the filter query during faceting
$facetqry->addExclude('inStockTag');

$facetqry = $facetset->createFacetQuery('genreFiction');
$facetqry->setQuery('genre_s: fiction');
//remove this exclude to find count of fiction books which include books not in stock as well.
//$facetqry->addExclude('inStockTag');
//fire the query and get the result
$resultSet = $client->select($query);
//number of results found
$found = $resultSet->getNumFound();
echo "Total Results Found : $found <br/>".PHP_EOL;
//display facet count for fantasy & fiction books
$fantasyCnt = $resultSet->getFacetSet()->getFacet('genreFantasy')->getValue();
$fictionCnt = $resultSet->getFacetSet()->getFacet('genreFiction')->getValue();
echo "Facet count : Fantasy = $fantasyCnt, Fiction = $fictionCnt <br/>".PHP_EOL;
?>
