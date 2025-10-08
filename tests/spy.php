<?php
require  __DIR__.'/utils/utils.api.php';
echo "Hello";

$API_PREFIX = "http://localhost/aiwl-app2/";
$API_COUNTRIES_LIST = [
    "url" => "get/countries/list",
    "method" => "GET"
];

$apiResponse = file_get_contents($API_PREFIX.$API_COUNTRIES_LIST["url"]);
?>