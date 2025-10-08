<?php
require  __DIR__.'/utils/utils.api.php';
require  __DIR__.'/utils/gen.report.php';

$API_PREFIX = "http://localhost/aiwl-app2/";
$API_COUNTRIES_LIST = [
    "url" => "get/countries/list",
    "method" => "GET"
];

$apiResponse = callApi($API_PREFIX.$API_COUNTRIES_LIST["url"], $API_COUNTRIES_LIST["method"]);

$generateReport = new GenerateReport("new-gen-report.html");
 $generateReport->apiTestTitle([
    "title" => "Get List of Countries",
    "url" => $API_COUNTRIES_LIST["url"],
    "method" => $API_COUNTRIES_LIST["method"],
    "testCases" =>[
        [
            "title"=>"Test the response is providing Countries List or not",
            "description"=>"We are hitting API and testing  the response is providing the countries list or not",
            "url"=>$API_COUNTRIES_LIST["url"],
            "method"=>$API_COUNTRIES_LIST["method"],
            "inputRequestBody"=>"-",
            "apiResponse"=>json_encode($apiResponse),
            "testResult"=>"",
            "status" => "PASSED",
            "comments"=>""
        ]
    ]
]);
?>