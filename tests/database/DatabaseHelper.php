<?php
class DatabaseHelper {
    private $apiPrefix;
    private $apiUrl;
    private $apiMethod;
    public function __construct($apiUrl, $apiMethod){
        $this->apiPrefix = $GLOBALS["API_DETAILS"]["prefix"];
        $this->apiUrl = $apiUrl;
        $this->apiMethod = $apiMethod;
    }

    // executeApiAndDBTest: Main Function that executes API and DB
    public function executeApiAndDBTest($apiAndDbTestCase){
        $apiData = $apiAndDbTestCase["api"];
        $dbData = $apiAndDbTestCase["database"];
        $testDetailsIndex = 0;
        foreach($dbData["details"] as $testDetails){
            switch( $testDetails["expectedResult"] ){
                case "CHECK_NO_EMPTY": {
                    $this->checkNoEmpty( $apiAndDbTestCase, $testDetailsIndex );
                }
            }
            $testDetailsIndex++;
        }
    }

    // NOTE: By Default, we will pass createdBy to all of the Tests.
    public function checkNoEmpty( $apiAndDbTestCase, $testDetailsIndex ){
        // 1) checks in the table, do we have any empty data inserted into it.
        // 2) I will hit an API with empty Data (API Test Data)
        // 3) Again, checks in the table, do we have any empty data inserted into it.
        // 4) If empty field exists, test is Failed
        echo "checkNoEmpty";
        print_r( $apiAndDbTestCase );
        // Print All Details for API and Database
        
        // 1) checks in the table, do we have any empty data inserted into it.
        $dbTitle = $apiAndDbTestCase["database"]["title"];
        $dbDesc = $apiAndDbTestCase["database"]["desc"];
        $dbTableName = $apiAndDbTestCase["database"]["details"][$testDetailsIndex]["tableName"];
        $dbExpectedResult = $apiAndDbTestCase["database"]["details"][$testDetailsIndex]["expectedResult"];

        // Get All Data from Table Schemas File
        $databaseConfig = $GLOBALS["DB_CONN"];
        $databaseQueryBuilder = new DatabaseQueryBuilder();
        $conditions = $databaseQueryBuilder->buildEmptyDataCheckQuery($dbTableName);
        $status = $databaseConfig->validateDb($dbTableName, $conditions, []);
        echo "IS_EMPTY: ".$status;
        // 2) I will hit an API with empty Data (API Test Data)
        $testCaseHelper = new TestCaseHelper($this->apiUrl, $this->apiMethod);
        $result = $testCaseHelper->runAPI($apiAndDbTestCase["api"]);
        print_r($result);
        

        // GET EXPECTED RESULTS in "database"->"details"
        
        

    }

    public function checkNoInsert($databaseTest, $apiTest){
        // 1) I will hit an API with what I am passing to it with a "createdBy" (APITestData)
        // 2) With same "createdBy", I will check in Database
        // 3) If it not exists, then the test is PASSED
    }

    public function checkDataInsert($databaseTest, $apiTest){
        // 1) I will hit an API with what I am passing to it with a "createdBy" (APITestData)
        // 2) With same "createdBy", I will check in Database
        // 3) If this exists, then test is Passed
    }

    public function checkDataUpdate($databaseTest, $apiTest){
        // 1) I will get existing Data from Database based on uniqueId mentioned & "createdBy" (starts with test-auto-%)
        // 2) I will hit an API with what I am passing to it (APITestData)
        // 3) Using uniqueId, I will get the updated Dtaa and compares it with existing one.
        // If they tally, the test is passed.
    }

    public function checkNoDuplicate($databaseTest, $apiTest){
        // 1) I will get existing Data from Database based on UniqueId mentioned & "createdBy" (starts with test-auito-%)
        // -------------------------------- CONFIRMS ONE COPY EXISTS ----------------------------------------------------
        // 2) I will hit an API with what I am passing to it (APITestData)
        // 3) Using UnqiueId, I will get the data again and confirms No Data got replicated from previous.
        // 4) If they tally, the test is PASSED
    }

    public function testInDatabase($dbTestCase, $apiTestData) {
        $db = $GLOBALS["DB_CONN"];
        $dbTestTitle = $dbTestCase["title"] ?? "";
        $dbTestDesc = $dbTestCase["desc"] ?? ""; 
        $expectedResultType = strtoupper(trim($dbTestCase["expectedResult"] ?? ""));
        echo "Running DB Test: $dbTestTitle\n";
        switch ($expectedResultType) {
            // ---------------------------
            //    CHECK: EMPTY DATA
            // ---------------------------
            case "CHECK_NO_EMPTY":
                // 
            // -------------------
            // CHECK: NO INSERTED DATA
            // -------------------
            case "CHECK_NO_INSERT":
                $table = $dbTestCase["table"] ?? "";
                $conditions = $dbTestCase["conditions"] ?? [];

                $result = $db->validateDb($table, $conditions, []);
                if (!$result) {
                    echo "✅ No record found (as expected)\n";
                } else {
                    echo "❌ Record unexpectedly found in $table\n";
                }
                break;
            // -------------------
            // CHECK: DATA INSERTED
            // -------------------
            case "CHECK_DATA_INSERT":
                $mappers = $dbTestCase["apiFieldToTblColumnMapper"] ?? [];

                foreach ($mappers as $map) {
                    $apiField = $map["apiField"];
                    $tblColumn = $map["tblColumnName"];

                    // Parse table.column
                    [$table, $col] = explode(".", $tblColumn);
                    $expectedValue = $apiTestData[$apiField] ?? null;

                    $result = $db->validateDb($table, [$col => $expectedValue], [$col => $expectedValue]);
                    if ($result) {
                        echo "✅ Value '$expectedValue' for '$col' found in $table\n";
                    } else {
                        echo "❌ Value '$expectedValue' for '$col' not found in $table\n";
                    }
                }
                break;
            // -------------------
            // CHECK: DATA UPDATED
            // -------------------
            case "CHECK_DATA_UPDATE":
                $table = $dbTestCase["table"] ?? "";
                $conditions = $dbTestCase["conditions"] ?? [];
                $expected = $dbTestCase["expectedValues"] ?? [];
                $result = $db->validateDb($table, $conditions, $expected);
                if ($result) {
                    echo "✅ Record updated correctly in $table\n";
                } else {
                    echo "❌ Record update verification failed in $table\n";
                }
                break;
            // -------------------
            // CHECK: NO DUPLICATE ENTRY
            // -------------------
            case "CHECK_NO_DUPLICATE":
                $table = $dbTestCase["table"] ?? "";
                $uniqueCols = $dbTestCase["uniqueColumns"] ?? [];
                foreach ($uniqueCols as $col) {
                    $val = $apiTestData[$col] ?? null;
                    $result = $db->validateDb($table, [$col => $val], []);
                    if ($result) {
                        echo "❌ Duplicate found for column '$col' with value '$val'\n";
                    } else {
                        echo "✅ No duplicate found for column '$col'\n";
                    }
                }
                break;
            // -------------------
            // DEFAULT CASE
            // -------------------
            default:
                echo "⚠️ Unknown DB test type: $expectedResultType\n";
                break;
        }
    }

    /**
     * Deletes all records from a given table.
     */
    public function cleanupTables($cleanData) {
        if (!empty($cleanData)) {
            foreach ($cleanData as $tableName) {
                try {
                    $database = $GLOBALS["DB_CONN"];
                    $result = $database->deleteFromTable($tableName, [
                        "createdBy" => $GLOBALS["TBL_COL_CREATEDBY"]
                    ]);
                    echo "DELETED ".$result["affectedRows"]." ROWS.";
                } catch (Exception $e) {
                    echo "[ERROR] Failed to clean table '$tableName': " . $e->getMessage() . "\n";
                    return false;
                }
            }
        }
    }
}
?>