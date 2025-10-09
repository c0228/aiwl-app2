<?php
// require  __DIR__.'/utils/utils.api.php';
require  __DIR__.'/utils/gen.report.php';
require __DIR__.'/apis/CountriesTest.php';

$API_PREFIX = "http://localhost/aiwl-app2/";
$API_INFO = [
    "countries" => [
        "url" => "get/countries/list",
        "method" => "GET"
    ]
];
$GEN_REPORT = new GenerateReport("new-gen-report.html");

$countriesTest = new CountriesTest();
$countriesList = $countriesTest->testExecute();

/*
$apiUrl = $API_PREFIX.$API_INFO["countries"]["url"];
$apiMethod = $API_INFO["countries"]["method"];
$apiResponse = callApi($apiUrl, $apiMethod);
$apiStatus = (count($apiResponse)>0)?'PASSED':'FAILED';


 $generateReport->apiTestTitle([
    "title" => "Get List of Countries",
    "url" => $apiUrl,
    "method" => $apiMethod,
    "testCases" =>[
        [
            "title"=>"Test the response is providing Countries List or not",
            "description"=>"We are hitting API and testing  the response is providing the countries list or not",
            "url"=>$apiUrl,
            "method"=>$apiMethod,
            "inputRequestBody"=>"-",
            "apiResponse"=>json_encode($apiResponse),
            "testResult"=>"",
            "status" => $apiStatus,
            "comments"=>""
        ]
    ]
]);
*/



?>