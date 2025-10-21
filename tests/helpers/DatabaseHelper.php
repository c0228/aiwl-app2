<?php
class DatabaseHelper {
    private $database;
    public function __construct() {
        $this->database = $GLOBALS["DB_CONN"];
    }
    public function testInDatabase($dbTestCase, $apiTestData){
        $dbTestTitle = $dbTestCase["title"] ?? "";
        $dbTestDesc = $dbTestCase["desc"] ?? "";
        echo "--------------------------------------------------------------------";
        echo "Title: ".$dbTestTitle;
        echo "--------------------------------------------------------------------";
        echo "Desc: ".$dbTestDesc;
        $dbTestApiTblColMapper = $dbTestCase["apiFieldToTblColumnMapper"] ?? [];
        foreach($dbTestApiTblColMapper as $apiFieldToTblColumnMapper){
            $apiField = $apiFieldToTblColumnMapper["apiField"] ?? "";
            $tblColumnName = $apiFieldToTblColumnMapper["tblColumnName"] ?? "";
            echo "ApiField: ".$apiField;
            echo "TblColumnName: ".$tblColumnName;
        }
        $dbTestExpectedResult = $dbTestCase["expectedResult"] ?? "";
        echo "ExpectedResult: ".$dbTestExpectedResult;
    }
    /**
     * Deletes all records from a given table.
     */
    public function cleanupTables($cleanData) {
        if (!empty($cleanData)) {
            foreach ($cleanData as $tableName) {
                try {
                    $affectedRows = $this->database->deleteFromTable($tableName, [
                        "createdBy" => $GLOBALS["TBL_COL_CREATEDBY"]
                    ]);
                    echo "DELETED ".$affectedRows." ROWS.";
                } catch (Exception $e) {
                    echo "[ERROR] Failed to clean table '$tableName': " . $e->getMessage() . "\n";
                    return false;
                }
            }
        }
    }
}
?>