<?php
class DatabaseHelper {
    private $database;
    public function __construct() {
        $this->database = $GLOBALS["DB_CONN"];
    }
    public function testInDatabase($dbTestCase, $apiTestData){
        print_r($dbTestCase);
        print_r($apiTestData);
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