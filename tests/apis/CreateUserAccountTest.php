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
    private $DB_TBL_NAME = 'user_accounts_info';
    private $genReport; // Report generator instance
    private $testData; // Test Data
    private $testCaseHelper;
    private $helper; // Helper object for API & DB operations
    private $apiUrl; // API endpoint for creating users
    private $apiMethod; // HTTP method (usually POST)
    private $apiResponses; // Stores raw API responses for reference
    private $testResults; // Stores structured test case results

    /**
     * Constructor initializes dependencies and loads API configuration.
     */
    public function __construct() {
        // STEP-1: Load Test Data
        $this->testData = DataLoader::load(__DIR__ . '/../data/UserAccountsData.json');
        // Initial Parameters
        $database = $GLOBALS["database"];
        $apiPrefix = $GLOBALS["API_PREFIX"] ?? '';
        $this->genReport = $GLOBALS["GEN_REPORT"] ?? null;
        $apiInfo = $GLOBALS["API_INFO"] ?? [];
        // Ensure API info for "createUser" is defined
        if (!isset($apiInfo["createUser"])) {
            throw new Exception("API_INFO key 'createUser' not defined!");
        }
        
        // Initialize TestHelper and test parameters
       //
       //  $this->helper = new TestHelper($database, $apiPrefix);
        $this->apiUrl = $apiInfo["createUser"]["url"];
        $this->apiMethod = $apiInfo["createUser"]["method"];
        $this->apiResponses = [];
        $this->testResults = [];
        // Create Object for Test Case Helper
        $this->testCaseHelper = new TestCaseHelper($this->apiUrl, $this->apiMethod);
        // database clean user_accounts_info
       //  $this->helper->cleanupTables($this->testData["init"]["database"]["clean"]);
    }
    /**
     * Main function that executes all test cases sequentially.
     * Also performs cleanup and reporting at the end.
     */
    public function testExecute() {
        // Step-1: Execute all defined test cases
        $testCasesList = array_keys( $this->testData );
        foreach($testCasesList as $testCaseName){
            // API Test
            $apiTest = $this->testData[$testCaseName]["api"];
            $apiTestResponse = $this->testCaseHelper->runAPI( $apiTest ); // Passed TestCase into it.

            // Database Test
            $databaseTest = $this->testData[$testCaseName]["database"];
            $databaseHelper = new DatabaseHelper();
            $databaseHelper->testInDatabase($databaseTest, $apiTest["data"] );

            // Dummy Setup for Now
            $apiTestResponse["testResult"] = "";
            $apiTestResponse["comments"] = "";
            // Results to be Added
            $this->addResult( $apiTestResponse );

        }

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
           // $this->helper->cleanupTestData("user_accounts_info", ["mobile" => $mobile]);
        }
    }
}

?>
