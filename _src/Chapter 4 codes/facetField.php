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
//get the facetset component
$facetset = $query->getFacetSet();
$facetset->createFacetField('author')->setField('author_s');
$facetset->createFacetField('genre')->setField('genre_s');
//return the top 5 facets only.
$facetset->setLimit(5);
//return all facets which have alteast 1 term in it.
$facetset->setMinCount(1);
//return all documents which do not have any value for the facet field
$facetset->setMissing(true);
//fire the query and get the result
$resultSet = $client->select($query);
//number of results found
$found = $resultSet->getNumFound();
echo "Results Found : $found<br/>".PHP_EOL;
//display facets for author
echo PHP_EOL."Facets for author:<br/>".PHP_EOL.PHP_EOL;
$facet_author = $resultSet->getFacetSet()->getFacet('author');
foreach($facet_author as $item => $count)
{
	echo $item.": [".$count."] <br/>".PHP_EOL;
}
//display facets for genre
echo PHP_EOL."Facets for genre:<br/>".PHP_EOL.PHP_EOL;
$facet_genre = $resultSet->getFacetSet()->getFacet('genre');
foreach($facet_genre as $item => $count)
{
	echo $item.": [".$count."] <br/>".PHP_EOL;
}


?>