<?php
include_once("vendor/autoload.php");

use Solarium\Client;
use Solarium\QueryType\Select\Query\Query as Select;

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

Class myQuery extends Select
{
	private $RESULTSPERPAGE = 5;

	//create the initial query
	protected function init()
	{
		parent::init();
		$this->setQuery('*:*');
		$this->setFields(array('id','name','price','author','score'));
		$this->setStart($this->getPageStart(1));
		$this->setRows($this->RESULTSPERPAGE);		
		$this->addSort('price', $this->getSortOrder('asc'));
	}

	function setMyQuery($query)
	{
		$this->setQuery($query);
	}

	//calculate page start
	private function getPageStart($pgno)
	{
		$pgst = ($pgno <= 1) ? 0 : $pgno-1 ;
		return $pgst*$this->RESULTSPERPAGE;		
	}

	//get sort order
	private function getSortOrder($sortOrder='asc')
	{
		return ($sortOrder == 'asc') ? self::SORT_ASC : self::SORT_DESC ;			
	}

	//function to reset all sorting
	private function resetSort()
	{
		$sorts = $this->getSorts();
		foreach($sorts as $sort)
		{
			$this->removeSort($sort);
		}
	}

	//change the current sorts - reset & add new sort
	function changeSort($sortField, $sortOrder)
	{
		$this->resetSort();
		$this->addSort($sortField, $this->getSortOrder($sortOrder));	
	}

	// add more sorting to current sorting order
	function addMoreSort($sortField, $sortOrder)
	{
		$this->addSort($sortField, $this->getSortOrder($sortOrder));
	}

	//change page
	function goToPage($pgno)
	{
		$this->setStart($this->getPageStart($pgno));		
	}
}

function displayResults($resultSet)
{
	//number of results found
	$found = $resultSet->getNumFound();
	echo PHP_EOL."<br/>Results Found : $found<br/>".PHP_EOL;
	//Show documents in the resultset.
	//iterate through all documents in a result.
	foreach($resultSet as $doc)
	{
		echo PHP_EOL."-------<br/>".PHP_EOL;
		echo PHP_EOL."ID : ".$doc->id;
		echo PHP_EOL."Name : ".$doc->name;
		echo PHP_EOL."Author : ".$doc->author;
		echo PHP_EOL."Price : ".$doc->price;
		echo PHP_EOL."Score : ".$doc->score;
	}
}

//create an instance of myquery
$query = new myQuery();
//user is searching for all books
$query->setMyQuery('cat:book');
//display the 1st page of results
echo "<b><br/>Searching for all books</b>".PHP_EOL;
$resultSet = $client->select($query);
displayResults($resultSet);

//user clicks on page 3
$query->goToPage(3);
//display results from 3rd page
echo "<b><br/>Going to page 3</b>".PHP_EOL;
$resultSet = $client->select($query);
displayResults($resultSet);

//user changes the sorting order to price descending
$query->changeSort('price','desc');
//change sorting order to price descending and show results from 3rd page
echo "<b><br/>Sorting by price descending</b>".PHP_EOL;
$resultSet = $client->select($query);
displayResults($resultSet);

//user clicks on page 1
$query->goToPage(1);
//display results from 3rd page
echo "<b><br/>Going back to page 1</b>".PHP_EOL;
$resultSet = $client->select($query);
displayResults($resultSet);

?>