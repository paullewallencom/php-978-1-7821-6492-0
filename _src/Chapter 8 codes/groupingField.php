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
$query->setFields(array('id','name','author','series_t','score','last_modified'));
//set number of rows to fetch
$query->setRows(25);
//get grouping component
$grp = $query->getGrouping();
//set fields to group by.
$grp->addField('author_s');
$grp->addField('genre_s');
//set number of items per group
$grp->setLimit(3);
// Also get the total number of groups.
$grp->setNumberOfGroups(true);
//fire the query and get the result
$resultSet = $client->select($query);
//get grouping from results
$grps = $resultSet->getGrouping();
foreach($grps as $grpKey => $grpFld)
{
	echo '<h1> Grouped by '.$grpKey.'</h1>';
	echo 'Total Matches: '.$grpFld->getMatches().'<br/>';
    echo 'Number of groups: '.$grpFld->getNumberOfGroups();

    foreach($grpFld as $grpVal) 
    {
    	//var_dump($grpVal);
        echo '<h2>'.$grpVal->getValue().' : '.$grpVal->getNumFound().'</h2>';
        echo '<hr/><table border=1>';
        foreach($grpVal as $doc) 
        {
            echo '<tr><td><b>id</b></td><td><b>name</b></td><td><b>author</b></td><td><b>series</b></td><td><b>score</b></td></tr>'.PHP_EOL;
			echo '<tr><td>'.$doc->id.'</td><td>'.$doc->name.'</td><td>'.$doc->author.'</td><td>'.$doc->series_t.'</td><td>'.$doc->score.'</td></tr>'.PHP_EOL;
        }
        echo '</table>';
    }
}


?>