<?php
require_once __DIR__.'/utils/DatabaseConfig.php';
require_once __DIR__.'/utils/ReportConfig.php';
require_once __DIR__.'/constants/BusinessConstants.php';
// require_once __DIR__ . '/helpers/TestHelper.php';
require_once __DIR__ . '/helpers/TestCaseHelper.php';
require_once __DIR__ . '/helpers/DatabaseHelper.php';
require_once __DIR__ . '/helpers/DataLoader.php';

require_once __DIR__.'/utils/utils.api.php';

require_once __DIR__.'/apis/CountriesTest.php';
require_once __DIR__.'/apis/StatesTest.php';
require_once __DIR__.'/apis/CreateUserAccountTest.php';

/*
$DB_SERVERNAME = 'localhost:3306';
$DB_NAME = 'iwlab';
$DB_USER = 'root';
$DB_PASSWORD = '';

$database = new Database($DB_SERVERNAME,$DB_NAME,$DB_USER,$DB_PASSWORD);

$API_PREFIX = "http://localhost/aiwl-app2/";
$API_INFO = [
    "countries" => [
        "url" => "get/countries/list",
        "method" => "GET"
    ],
    "states" => [
        "url" => "get/{country}/data",
        "method" => "GET"
    ],
    "createUser" => [
        "url" => "auth/user/register",
        "method" => "POST"
    ]
];
$GEN_REPORT = new GenerateReport("new-gen-report.html");
*/

// Testing Countries API
$countriesTest = new CountriesTest();
$countriesList = $countriesTest->testExecute();

// Testing States API
$statesTest = new StatesTest();
$statesTest->testExecute($countriesList);

// Create User Account
$createUserAccountTest = new CreateUserAccountTest();
$createUserAccountTest->testExecute();
// Testing Create User Account API
/* $testUsers = [
    [
        "name" => "Alice Johnson",
        "country" => "USA",
        "state" => "California",
        "mobile" => "9876543210",
        "balance" => "500"
    ],
    [
        "name" => "Ravi Kumar",
        "country" => "India",
        "state" => "Telangana",
        "mobile" => "9123456789",
        "balance" => "1000"
    ]
];
$userTest = new CreateUserAccountTest($testUsers);
$userTest->testExecute(); */

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