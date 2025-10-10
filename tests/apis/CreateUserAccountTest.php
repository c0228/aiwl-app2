<?php
/**
 * Class: CreateUserAccountTest
 * ----------------------------------------------------
 * ✅ Purpose:
 * This class is designed to automatically test the "Create User Account" API.
 * It covers multiple scenarios like:
 *   - Empty data
 *   - Required field checks
 *   - Missing field validation
 *   - Duplicate mobile handling
 *
 * ✅ Workflow:
 * 1. Uses TestHelper to execute API requests.
 * 2. Validates API responses and optional database entries.
 * 3. Cleans up inserted test data after completion.
 * 4. Generates a structured test report for documentation.
 */

class CreateUserAccountTest {
    private $genReport; // Report generator instance
    private $helper; // Helper object for API & DB operations
    private $apiUrl; // API endpoint for creating users
    private $apiMethod; // HTTP method (usually POST)
    private $apiResponses; // Stores raw API responses for reference
    private $testResults; // Stores structured test case results

    /**
     * Constructor initializes dependencies and loads API configuration.
     */
    public function __construct() {
        $database = $GLOBALS["database"];
        $apiPrefix = $GLOBALS["API_PREFIX"] ?? '';
        $this->genReport = $GLOBALS["GEN_REPORT"] ?? null;
        $apiInfo = $GLOBALS["API_INFO"] ?? [];
        // Ensure API info for "createUser" is defined
        if (!isset($apiInfo["createUser"])) {
            throw new Exception("API_INFO key 'createUser' not defined!");
        }
        // Initialize TestHelper and test parameters
        $this->helper = new TestHelper($database, $apiPrefix);
        $this->apiUrl = $apiInfo["createUser"]["url"];
        $this->apiMethod = $apiInfo["createUser"]["method"];
        $this->apiResponses = [];
        $this->testResults = [];
    }

    /**
     * Main function that executes all test cases sequentially.
     * Also performs cleanup and reporting at the end.
     */
    public function testExecute() {
        // Step-1: Execute all defined test cases
        $this->testEmptyData();
        $this->testMobileRequiredBalanceOptional();
        $this->testMissingFields();
        $this->testDuplicateMobile();

        // Step-2: After all tests, clean up test data
        $this->cleanupInsertedUsers();

        // Step-3: Generate report
        if ($this->genReport) {
            $this->genReport->apiTestTitle([
                "title" => "Create New User Account Test Suite",
                "url" => $this->apiUrl,
                "method" => $this->apiMethod,
                "testCases" => $this->testResults
            ]);
        }

        // Return collected API responses
        return $this->apiResponses;
    }

    // -------------------- Individual Test Cases --------------------
    /**
     * Test Case 1: Sends completely empty data to the API.
     * Expected: Validation failure or missing field error.
     */
    private function testEmptyData() {
        $this->addResult(
            $this->helper->runApiTestCase(
                "Test Case 1: Empty Data",
                $this->apiUrl,
                $this->apiMethod,
                [],
                ""
            )
        );
    }

    /**
     * Test Case 2: Checks if 'mobile' is mandatory and 'balance' is optional.
     * Expected: Successful registration (USER_NEW_REGISTERED).
     */
    private function testMobileRequiredBalanceOptional() {
        $user = ["name" => "Test User 1", "country" => "USA", "state" => "California", "mobile" => "1234567890"];
        $this->addResult(
            $this->helper->runApiTestCase(
                "Test Case 2: Mobile required, balance optional",
                $this->apiUrl,
                $this->apiMethod,
                $user,
                'USER_NEW_REGISTERED',
                "user_accounts_info",
                ["mobile" => $user["mobile"]],
                $user
            )
        );
    }

     /**
     * Test Case 3: Runs multiple sub-tests for missing required fields.
     * Each variation removes one key field (name, country, state, etc.)
     */
    private function testMissingFields() {
        $cases = [
            ["country" => "India", "state" => "Telangana", "mobile" => "9876543210"],
            ["name" => "Test User 3", "state" => "Delhi", "mobile" => "9876543211"],
            ["name" => "Test User 4", "country" => "India", "mobile" => "9876543212"],
            ["name" => "Test User 5", "country" => "India", "state" => "Karnataka", "mobile" => "9876543213", "balance" => null]
        ];
        foreach ($cases as $i => $case) {
            $this->addResult(
                $this->helper->runApiTestCase(
                    "Test Case 3." . ($i+1) . ": Missing fields",
                    $this->apiUrl,
                    $this->apiMethod,
                    $case,
                    ""
                )
            );
        }
    }

    /**
     * Test Case 4: Attempts to register a user with an already existing mobile number.
     * Expected: Failure or duplicate record message.
     */
    private function testDuplicateMobile() {
        $user = ["name" => "Test User 1", "country" => "USA", "state" => "California", "mobile" => "1234567890"];
        $this->addResult(
            $this->helper->runApiTestCase(
                "Test Case 4: Duplicate Mobile",
                $this->apiUrl,
                $this->apiMethod,
                $user,
                ""
            )
        );
    }

    // -------------------- Utility Methods --------------------
    /**
     * Adds individual test result to global arrays for final report.
     */
    private function addResult($result) {
        $this->testResults[] = $result;
        $this->apiResponses[] = json_decode($result["apiResponse"], true);
    }

    /**
     * Cleans up any test data inserted into the database after execution.
     * Prevents polluting real database with test users.
     */
    private function cleanupInsertedUsers() {
        $mobiles = ["1234567890", "9876543210", "9876543211", "9876543212", "9876543213"];
        foreach ($mobiles as $mobile) {
            $this->helper->cleanupTestData("user_accounts_info", ["mobile" => $mobile]);
        }
    }
}

?>
