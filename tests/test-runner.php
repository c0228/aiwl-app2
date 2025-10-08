<?php
require  __DIR__.'/utils/utils.api.php';
require  __DIR__.'/utils/gen.report.php';

$API_PREFIX = "http://localhost/aiwl-app2/";
$API_COUNTRIES_LIST = [
    "url" => "get/countries/list",
    "method" => "GET"
];

$apiResponse = callApi($API_PREFIX.$API_COUNTRIES_LIST["url"], $API_COUNTRIES_LIST["method"]);

?>