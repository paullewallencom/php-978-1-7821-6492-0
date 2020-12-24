<?php
$url = "http://localhost:8080/solr/collection1/admin/ping/?wt=json";
$curl = curl_init($url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
$output = curl_exec($curl);
$data = json_decode($output, true);
//var_dump($data);
echo "Ping Status : ".$data["status"].PHP_EOL;
?>