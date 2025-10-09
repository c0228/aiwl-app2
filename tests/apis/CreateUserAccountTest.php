<?php

class CreateUserAccountTest {
    private $genReport;
    private $apiPrefix;
    private $apiUrl;
    private $apiMethod;
    private $apiResponses;
    private $testResults;
    private $database;

    public function __construct() {
        $this->database = $GLOBALS["database"];
        $this->apiPrefix = $GLOBALS["API_PREFIX"] ?? '';
        $this->genReport = $GLOBALS["GEN_REPORT"] ?? null;
        $apiInfo = $GLOBALS["API_INFO"] ?? [];

        if (!isset($apiInfo["createUser"])) {
            throw new Exception("API_INFO key 'createUser' not defined!");
        }

        $this->apiUrl = $apiInfo["createUser"]["url"];
        $this->apiMethod = $apiInfo["createUser"]["method"];
        $this->apiResponses = [];
        $this->testResults = [];
    }

    public function testExecute() {
        $this->testEmptyData();
        $this->testMobileRequiredBalanceOptional();
        $this->testMissingFields();
        $this->testDuplicateMobile();

        // Generate report after all tests
        if ($this->genReport) {
            $this->genReport->apiTestTitle([
                "title" => "Create New User Account Test Suite",
                "url" => $this->apiUrl,
                "method" => $this->apiMethod,
                "testCases" => $this->testResults
            ]);
        }

        return $this->apiResponses;
    }

    // ------------------ Private Test Cases ------------------

    private function testEmptyData() {
        $this->runSingleTest([], "Test Case 1: API called with empty data");
    }

    private function testMobileRequiredBalanceOptional() {
        $userData = [
            "name" => "Test User 1",
            "country" => "USA",
            "state" => "California",
            "mobile" => "1234567890"
        ];
        $this->runSingleTest($userData, "Test Case 2: Mobile required, balance optional");
    }

    private function testMissingFields() {
        $fieldsToTest = [
            ["country" => "India", "state" => "Telangana", "mobile" => "9876543210"], // missing name
            ["name" => "Test User 3", "state" => "Delhi", "mobile" => "9876543211"],   // missing country
            ["name" => "Test User 4", "country" => "India", "mobile" => "9876543212"], // missing state
            ["name" => "Test User 5", "country" => "India", "state" => "Karnataka", "mobile" => "9876543213", "balance" => null] // missing balance
        ];

        foreach ($fieldsToTest as $i => $data) {
            $this->runSingleTest($data, "Test Case 3: Missing fields - Case ".($i+1));
        }
    }

    private function testDuplicateMobile() {
        $userData = [
            "name" => "Test User 1",
            "country" => "USA",
            "state" => "California",
            "mobile" => "1234567890" // same mobile as Test Case 2
        ];
        $this->runSingleTest($userData, "Test Case 4: Duplicate data with same mobile");
    }

    // ------------------ Helper Function ------------------

    private function runSingleTest($userData, $title) {
        $fullUrl = $this->apiPrefix . $this->apiUrl;
        $response = callApi($fullUrl, $this->apiMethod, $userData);

        // Determine test status
        $status = (is_array($response) && isset($response['message']) && $response['message'] === 'USER_NEW_REGISTERED') 
            ? 'PASSED' 
            : 'FAILED';

        // Optional DB validation for successful insert
        $dbValidation = false;
        if ($status === 'PASSED' && !empty($userData['mobile'])) {
            $expected = $userData;
            unset($expected['balance']); // balance is optional
            $dbValidation = $this->database->validateDb("user_accounts_info", ["mobile" => $userData['mobile']], $expected);
            if (!$dbValidation) $status = 'FAILED';
        }

        $this->apiResponses[] = $response;
        $this->testResults[] = [
            "title" => $title,
            "description" => "API called with: ".json_encode($userData),
            "url" => $fullUrl,
            "method" => $this->apiMethod,
            "inputRequestBody" => json_encode($userData),
            "apiResponse" => json_encode($response),
            "testResult" => $dbValidation ? "DB Verified" : "",
            "status" => $status,
            "comments" => $dbValidation ? "" : "Check DB insert"
        ];
    }
}

?>
