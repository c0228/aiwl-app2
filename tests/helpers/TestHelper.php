<?php
/**
 * Class: TestHelper
 * ----------------------------------------------------
 * Purpose:
 * This helper class centralizes all reusable testing logic.
 * It provides:
 *   - A consistent way to call APIs (via callApi function)
 *   - Standardized validation of API responses
 *   - Optional database validation and cleanup utilities
 *
 * This allows multiple API test suites (e.g. CreateUser, UpdateUser, etc.)
 * to share the same testing logic without code duplication.
 */
class TestHelper {
    private $database; // Database instance for validation and cleanup
    private $apiPrefix; // Base API prefix for all endpoints

    /**
     * Constructor initializes DB connection and API base path.
     */
    public function __construct($database, $apiPrefix) {
        $this->database = $database;
        $this->apiPrefix = $apiPrefix;
    }

    /**
     * Runs a single API test case.
     * ------------------------------------------------
     * @param string $title             Test case title
     * @param string $apiUrl            API endpoint URL (relative path)
     * @param string $apiMethod         HTTP method (GET, POST, PUT, etc.)
     * @param array  $requestData       API input payload
     * @param string $expectedMessage   Expected success message from API
     * @param string|null $dbTable      (Optional) DB table to validate
     * @param array|null $dbConditions  (Optional) WHERE condition for validation
     * @param array|null $dbExpected    (Optional) Expected DB data for comparison
     *
     * @return array Returns structured test result for reporting
     */

    public function runApiTestCase($title, $apiUrl, $apiMethod, $requestData, $expectedMessage, 
        $dbTable = null, $dbConditions = null, $dbExpected = null) {
        // Full API URL (prefix + endpoint)
        $fullUrl = $this->apiPrefix . $apiUrl;
        // Execute the API call
        $response = callApi($fullUrl, $apiMethod, $requestData);

        // Step 1: Validate API response message
        $status = (is_array($response) && isset($response['message']) && $response['message'] === $expectedMessage)
            ? 'PASSED'
            : 'FAILED';

        // Step 2: Optionally validate DB record if API passed
        $dbValidation = false;
        if ($status === 'PASSED' && $dbTable && $dbConditions && $dbExpected) {
            $dbValidation = $this->database->validateDb($dbTable, $dbConditions, $dbExpected);
            if (!$dbValidation) {
                $status = 'FAILED'; // Fail if DB data mismatch
            }
        }

        // Step 3: Return structured data for report
        return [
            "title" => $title,
            "description" => "API called with: " . json_encode($requestData),
            "url" => $fullUrl,
            "method" => $apiMethod,
            "inputRequestBody" => json_encode($requestData, JSON_PRETTY_PRINT),
            "apiResponse" => json_encode($response, JSON_PRETTY_PRINT),
            "testResult" => $dbValidation ? "DB Verified" : "",
            "status" => $status,
            "comments" => $dbValidation ? "" : "Check DB insert"
        ];
    }

    /**
     * Deletes test data safely from any table.
     * ------------------------------------------------
     * @param string $table Table name
     * @param array  $conditionArray Associative array of WHERE conditions
     * @return bool True if row deleted, false otherwise
     */
    public function cleanupTestData($table, $conditionArray) {
        return $this->database->deleteFromTable($table, $conditionArray);
    }
}

?>
