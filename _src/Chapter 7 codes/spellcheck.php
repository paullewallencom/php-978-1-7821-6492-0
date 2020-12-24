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
//set number of rows to fetch
$query->setQuery('stepen kang');
$query->setRows(0);
//get the more like this component
$spellChk = $query->getSpellcheck();
//$spellChk->setQuery('martan');
// no of suggestions to get
$spellChk->setCount(5);
$spellChk->setCollate(true);
$spellChk->setExtendedResults(true);
$spellChk->setCollateExtendedResults(true);

// this executes the query and returns the result
$resultset = $client->select($query);
// get spellcheck component from the result.
$spellChkResult = $resultset->getSpellcheck();
//var_dump($spellChkResult); die;
echo '<h1>Correctly spelled? => ';
if ($spellChkResult->getCorrectlySpelled()) {
    echo 'yes';
}else{
    echo 'no';
}
echo '</h1>';

echo '<h1>Suggestions</h1>';
foreach($spellChkResult as $suggestion) {
    echo 'NumFound: '.$suggestion->getNumFound().'<br/>';
    echo 'StartOffset: '.$suggestion->getStartOffset().'<br/>';
    echo 'EndOffset: '.$suggestion->getEndOffset().'<br/>';
    echo 'OriginalFrequency: '.$suggestion->getOriginalFrequency().'<br/>';    
    foreach ($suggestion->getWords() as $word) {
        echo '-----<br/>';
        echo 'Frequency: '.$word['freq'].'<br/>';
        echo 'Word: '.$word['word'].'<br/>';
    }

    echo '<hr/>';
}

$collations = $spellChkResult->getCollations();
echo '<h1>Collations</h1>';
foreach($collations as $collation) {
    echo 'Query: '.$collation->getQuery().'<br/>';
    echo 'Hits: '.$collation->getHits().'<br/>';
    echo '<hr/>';
    echo 'Corrections:<br/>';
    foreach($collation->getCorrections() as $input => $correction) {
        echo $input . ' => ' . $correction .'<br/>';
    }
}
?>