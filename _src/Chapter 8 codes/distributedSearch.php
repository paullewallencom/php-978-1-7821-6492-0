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
$query->setFields(array('id','name','author','series_t','score','price'));
//set number of rows to fetch
$query->setRows(50);
//$query->addSort('price'=>'desc');
//get distributed search component
$dSearch = $query->getDistributedSearch();
//add shards to search upon
$dSearch->addShard('shard1','localhost:8080/solr');
$dSearch->addShard('shard2','localhost:8983/solr');
//fire the query and get the result
$resultSet = $client->select($query);
echo 'Found : '.$resultSet->getNumFound().'<br/>';
echo '<hr/><table border=1>';
echo '<tr><td><b>id</b></td><td><b>name</b></td><td><b>author</b></td><td><b>series</b></td><td><b>price</b></td></tr>'.PHP_EOL;
foreach($resultSet as $doc)
{
	echo '<tr><td>'.$doc->id.'</td><td>'.$doc->name.'</td><td>'.$doc->author.'</td><td>'.$doc->series_t.'</td><td>'.$doc->price.'</td></tr>'.PHP_EOL;
}
echo '</table>';

?>