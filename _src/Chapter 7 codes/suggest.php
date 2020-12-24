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

//pick up the variable from url
$var = $_GET["sug"];
if(strlen($var)<=2)
{   
    echo "Provide at least 3 characters";
    die;
}

$client = new Solarium\Client($config);
//create a suggester query
$suggestqry = $client->createSuggester();
//set the search handler to use
$suggestqry->setHandler('suggest');
//set the dictionary to use
$suggestqry->setDictionary('suggest');
//set number of rows to fetch
$suggestqry->setQuery($var);
// no of suggestions to get
$suggestqry->setCount(5);
$suggestqry->setCollate(true);
$suggestqry->setOnlyMorePopular(true);
// this executes the query and returns the suggestions
$resultset = $client->suggester($suggestqry);
//display the original query
echo "Query : ".$suggestqry->getQuery();
echo '<hr/>';
// display suggestions for each term
foreach ($resultset as $term => $termResult) {
    echo '<b>' . $term . '</b><br/>';
    echo 'NumFound: '.$termResult->getNumFound().'<br/>';
    echo 'StartOffset: '.$termResult->getStartOffset().'<br/>';
    echo 'EndOffset: '.$termResult->getEndOffset().'<br/>';
    echo 'Suggestions:<br/>';
    foreach($termResult as $result){
        echo '-> '.$result.'<br/>';
    }
    echo "-------------------<br/>";    
}

// display collation
echo 'Collation: '.$resultset->getCollation();

?>