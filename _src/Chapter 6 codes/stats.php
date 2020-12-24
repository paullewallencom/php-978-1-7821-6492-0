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
//$query->setFields(array('id','name','author','series_t','score','price'));
//Do not fetch any rows as we are interested only in the statistics
$query->setRows(0);
//get the stats component
$statsq = $query->getStats();
//get statistics of price and facet by price and inStock
$statsq->createField('price')->addFacet('price')->addFacet('inStock');
//execute query and get resultset
$resultset = $client->select($query);
//fetch the stats component from the resultset
$statsResult = $resultset->getStats();
//display the result
//loop throught all fields in the result
foreach($statsResult as $field)
{
    echo '<b>Statistics for '.$field->getName().'</b><br/>';
    echo 'Min: ' . $field->getMin() . '<br/>';
    echo 'Max: ' . $field->getMax() . '<br/>';
    echo 'Sum: ' . $field->getSum() . '<br/>';
    echo 'Count: ' . $field->getCount() . '<br/>';
    echo 'Missing: ' . $field->getMissing() . '<br/>';
    echo 'SumOfSquares: ' . $field->getSumOfSquares() . '<br/>';
    echo 'Mean: ' . $field->getMean() . '<br/>';
    echo 'Stddev: ' . $field->getStddev() . '<br/>';

    echo '<br/><b>Facets</b><br/>';
    foreach ($field->getFacets() as $fld => $fct) 
    {
        echo '<hr/><b>Facet for '.$fld.'</b><br/>';
        foreach ($fct as $fctStats) {
            echo '<b>' . $fld . ' = ' . $fctStats->getValue() . '</b><br/>';
            echo 'Min: ' . $fctStats->getMin() . '<br/>';
            echo 'Max: ' . $fctStats->getMax() . '<br/>';
            echo 'Sum: ' . $fctStats->getSum() . '<br/>';
            echo 'Count: ' . $fctStats->getCount() . '<br/>';
            echo 'Missing: ' . $fctStats->getMissing() . '<br/>';
            echo 'SumOfSquares: ' . $fctStats->getSumOfSquares() . '<br/>';
            echo 'Mean: ' . $fctStats->getMean() . '<br/>';
            echo 'Stddev: ' . $fctStats->getStddev() . '<br/><br/>';
        }
    }
}
?>