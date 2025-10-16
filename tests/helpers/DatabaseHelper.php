<?php
class DatabaseHelper {
    private $database;
    public function __construct() {
        $this->database = $GLOBALS["database"];
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
                    $query = "DELETE FROM `$tableName` WHERE createdBy='"."'";
                    $stmt = $this->database->prepare($query);
                    $stmt->execute();
                    echo "[CLEANUP] Table '$tableName' cleaned successfully.\n";
                    return true;
                } catch (Exception $e) {
                    echo "[ERROR] Failed to clean table '$tableName': " . $e->getMessage() . "\n";
                    return false;
                }
            }
        }
    }
}
?>