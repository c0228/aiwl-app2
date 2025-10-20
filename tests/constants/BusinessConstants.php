<?php
// BUSINESS CONSTANTS are Global Constants that are picked from here
// SECTION #1: DATABASE related Constants
$DB_SERVERNAME = 'localhost:3306';
$DB_NAME = 'iwlab';
$DB_USER = 'root';
$DB_PASSWORD = '';

$TBL_USER_ACCOUNTS_INFO = 'user_accounts_info';
$TBL_COL_CREATEDBY = 'app-auto-test';

$DB_CONN = new DatabaseConfig($DB_SERVERNAME,$DB_NAME,$DB_USER,$DB_PASSWORD);

// SECTION #2: API related Constants
$API_DETAILS = [
    "prefix" => "http://localhost/aiwl-app2/",
    "info" => [
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
     ]
    ];

// SECTION #3: Load Test Data Files
$USER_ACCOUNT_TESTDATAFILE = __DIR__.'/../data/UserAccountsData.json';

// SECTION #4: GENERATE REPORTS
$GEN_REPORT_FILE = 'new-gen-report.html';
$GEN_REPORT_OBJ = new GenerateReport($GEN_REPORT_FILE);


?>