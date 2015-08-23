<?php 

include "function.php";
include "algoliasearch/algoliasearch.php";

$client = new \AlgoliaSearch\Client($app_id, $api_key);

//var_dump($client); die;

$index = $client->initIndex("site_wide_search_index_production");

var_dump($index); die;

var_dump($index->search('home'));