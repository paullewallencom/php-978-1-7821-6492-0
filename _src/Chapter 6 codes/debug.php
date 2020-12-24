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
$query->setQuery('cat:book OR author:martin^2');
//set fields to fetch
$query->setFields(array('id','name','author','series_t','score','price'));
//set number of rows to fetch
$query->setRows(5);
//get the debugging component
$debugq = $query->getDebug();
$debugq->setExplainOther('author:king');
//fire the query and get the result
$resultSet = $client->select($query);
$dResultSet = $resultSet->getDebug();

// display the debug results
echo '<h1>Debug data</h1>';
echo 'Querystring: ' . $dResultSet->getQueryString() . '<br/>';
echo 'Parsed query: ' . $dResultSet->getParsedQuery() . '<br/>';
echo 'Query parser: ' . $dResultSet->getQueryParser() . '<br/>';
echo 'Other query: ' . $dResultSet->getOtherQuery() . '<br/>';

echo '<h2>Explain data</h2>';
//var_dump($dResultSet->getExplain());
foreach ($dResultSet->getExplain() as $key => $explanation) {
    echo '<h3>Document key: ' . $key . '</h3>';
    echo 'Value: ' . $explanation->getValue() . '<br/>';
    echo 'Match: ' . (($explanation->getMatch() == true) ? 'true' : 'false')  . '<br/>';
    echo 'Description: ' . $explanation->getDescription() . '<br/>';
    echo '<h4>Details</h4>';
    foreach ($explanation as $detail) {
        echo 'Value: ' . $detail->getValue() . '<br/>';
        echo 'Match: ' . (($detail->getMatch() == true) ? 'true' : 'false')  . '<br/>';
        echo 'Description: ' . $detail->getDescription() . '<br/>';
        echo '<hr/>';
    }
}

echo '<h2>ExplainOther data</h2>';
foreach ($dResultSet->getExplainOther() as $key => $explanation) {
    echo '<h3>Document key: ' . $key . '</h3>';
    echo 'Value: ' . $explanation->getValue() . '<br/>';
    echo 'Match: ' . (($explanation->getMatch() == true) ? 'true' : 'false')  . '<br/>';
    echo 'Description: ' . $explanation->getDescription() . '<br/>';
    echo '<h4>Details</h4>';
    foreach ($explanation AS $detail) {
        echo 'Value: ' . $detail->getValue() . '<br/>';
        echo 'Match: ' . (($detail->getMatch() == true) ? 'true' : 'false')  . '<br/>';
        echo 'Description: ' . $detail->getDescription() . '<br/>';
        echo '<hr/>';
    }
}

echo '<h2>Timings (in ms)</h2>';
echo 'Total time: ' . $dResultSet->getTiming()->getTime() . '<br/>';
echo '<h3>Phases</h3>';
foreach ($dResultSet->getTiming()->getPhases() as $phaseName => $phaseData) {
    echo '<h4>' . $phaseName . '</h4>';
    foreach ($phaseData as $subType => $time) {
        echo $subType . ': ' . $time . '<br/>';
    }
    echo '<hr/>';
}

?>