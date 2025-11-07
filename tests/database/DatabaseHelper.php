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
        $checkDataInsertValidation  = new CheckDataInsertValidation($this->apiUrl, $this->apiMethod);
        $checkDataUpdateValidation  = new CheckDataUpdateValidation($this->apiUrl, $this->apiMethod);
        $checkNoDuplicateValidation  = new CheckNoDuplicateValidation($this->apiUrl, $this->apiMethod);
        $checkNoEmptyValidation  = new CheckNoEmptyValidation($this->apiUrl, $this->apiMethod);
        $checkNoInsertValidation  = new CheckNoInsertValidation($this->apiUrl, $this->apiMethod);
        foreach($dbData["details"] as $testDetails){
            switch( $testDetails["expectedResult"] ){
                case "CHECK_DATA_INSERT": {
                    $checkDataInsertValidation->validate( $apiAndDbTestCase, $testDetailsIndex );
                }
                case "CHECK_DATA_UPDATE": {
                    $checkDataUpdateValidation->validate( $apiAndDbTestCase, $testDetailsIndex );
                }
                case "CHECK_NO_EMPTY": {
                    $checkNoDuplicateValidation->validate( $apiAndDbTestCase, $testDetailsIndex );
                }
                case "CHECK_NO_DUPLICATE": {
                    $checkNoEmptyValidation->validate( $apiAndDbTestCase, $testDetailsIndex );
                }
                case "CHECK_NO_INSERT": {
                    $checkNoInsertValidation->validate( $apiAndDbTestCase, $testDetailsIndex );
                }
            }
            $testDetailsIndex++;
        }
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