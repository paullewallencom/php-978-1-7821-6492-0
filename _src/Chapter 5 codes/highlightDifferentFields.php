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
$query->setQuery('harry');
//set fields to fetch
$query->setFields(array('id','name','author','series_t','score','last_modified'));
//set number of rows to fetch
$query->setRows(25);
//get highlighting component
$hl = $query->getHighlighting();
//set fields to highlight
$hl->getField('name')->setSimplePrefix('<b>')->setSimplePostfix('</b>');
$hl->getField('series_t')->setSimplePrefix('<i>')->setSimplePostfix('</i>');
// highlight upto 2 snippets
$hl->setSnippets(2);
// enables highlighting for range, wildcard, fuzzy and prefix queries.
$hl->setHighlightMultiTerm(true);
//fire the query and get the result
$resultSet = $client->select($query);
//get highlighting of results
$hlresults = $resultSet->getHighlighting();
//number of results found
$found = $resultSet->getNumFound();
echo "Results Found : $found<br/>".PHP_EOL;
echo '<table border=1>';
foreach($resultSet as $doc)
{
	$hldoc = $hlresults->getResult($doc->id);
	//var_dump($hldoc);
	$hlname = implode(',',$hldoc->getField('name'));
	$hlseries = implode(',',$hldoc->getField('series_t'));
	
	echo '<tr><td><b>id</b></td><td><b>name</b></td><td><b>author</b></td><td><b>series</b></td><td><b>score</b></td></tr>'.PHP_EOL;
	echo '<tr><td>'.$doc->id.'</td><td>'.$hlname.'</td><td>'.$doc->author.'</td><td>'.$hlseries.'</td><td>'.$doc->score.'</td></tr>'.PHP_EOL;

}
echo '</table>';

?>