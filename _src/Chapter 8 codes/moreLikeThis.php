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
$query->setRows(10);
//get the more like this component
$mltquery = $query->getMoreLikeThis();
//set the fields on which mlt will work
$mltquery->setFields('author,series_t');
//set min doc freq & min term freq
$mltquery->setMinimumDocumentFrequency(1);
$mltquery->setMinimumTermFrequency(1);
//enable boosting 
$mltquery->setBoost(true);
//no of mlt docs to return
$mltquery->setCount(2);
//boost query
$mltquery->setQueryFields('author^1.5');
// execute the query and get the result.
$resultset = $client->select($query);
//get more like this component from the result
$mltResult = $resultset->getMoreLikeThis();
//display the documents
foreach($resultset as $doc)
{
    echo '<br/>'.$doc->id.' : '.$doc->name.' by '.$doc->author."<br/>".PHP_EOL;
    echo 'Series : '.$doc->series_t.', price : '.$doc->price.',Score: '.$doc->score."<br/>".PHP_EOL;
    $mltdocs = $mltResult->getResult($doc->id);
    if($mltdocs)
    {
        echo '<br/>More like this found : '.$mltdocs->getNumFound().', Fetched : '.count($mltdocs)."<br/>".PHP_EOL;
        foreach($mltdocs as $mltdoc)
        {
            echo '==>> '.$mltdoc->id.' : '.$mltdoc->name.' by '.$mltdoc->author.', price : '.$mltdoc->price."<br/>".PHP_EOL;
        }
    }
    echo PHP_EOL."<hr/>";
}
?>