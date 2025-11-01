<?php

class DatabaseConfig
{
    private $logger;
	private $serverName;
	private $databaseName;
	private $userName;
	private $password;
	
    function __construct($serverName,$databaseName,$userName,$password) {
	   $this->serverName=$serverName;
	   $this->databaseName=$databaseName;
	   $this->userName=$userName;
	   $this->password=$password;
    }
    
    function dbinteraction() {
	   $conn = new mysqli($this->serverName,$this->userName,$this->password,$this->databaseName);
        if ($conn->connect_error) {   die("Connection failed: " . $conn->connect_error); } 
        return $conn;
    }

    function validateDb($table, $conditions, $expected) {
        $conn = $this->dbinteraction();

        // Build WHERE clause with proper escaping
        $where = [];
        foreach ($conditions as $col => $val) {
            $val = $val ?? ""; // convert null to empty string
            $where[] = $col . " = '" . $conn->real_escape_string($val) . "'";
        }
        $sql = "SELECT * FROM $table WHERE " . implode(' AND ', $where);

        $result = $conn->query($sql);
        if (!$result) {
            $conn->close();
            die("Invalid query: " . $conn->error);
        }

        $row = $result->fetch_assoc();
        $conn->close();

        if (!$row) {
            return false; // No row matched
        }

        // Check if all expected key-value pairs match
        foreach ($expected as $key => $value) {
            if (!array_key_exists($key, $row) || $row[$key] != $value) {
                return false;
            }
        }
        return true;
    }

        /**
     * Delete records from a table based on given conditions.
     * 
     * @param string $table Table name
     * @param array $conditions Key-value pairs for WHERE clause
     * @return bool True if delete successful, false otherwise
     */
    function deleteFromTable($table, $conditions) {
        $conn = $this->dbinteraction();

        // Build WHERE clause safely
        $where = [];
        foreach ($conditions as $col => $val) {
            $where[] = $col . " = '" . $conn->real_escape_string($val) . "'";
        }

        $sql = "DELETE FROM $table WHERE " . implode(' AND ', $where);

        $result = $conn->query($sql);
        if ($result === false) {
            error_log("Database delete failed: " . $conn->error);
            $conn->close();
            return false;
        }

        $affectedRows = $conn->affected_rows;
        $conn->close();

        return $affectedRows;
    }

    /**
     * Generate JSON schema files for all tables in the database.
     *
     * @param string $outputDir Directory to save JSON files
     * @return void
     */
    function generateSchemaJSON($outputDir = "./schemas") {
        $conn = $this->dbinteraction();

        // Create output directory if not exists
        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0777, true);
        }

        $tables = $conn->query("SHOW TABLES");
        if (!$tables) {
            die("Error fetching tables: " . $conn->error);
        }

        while ($tableRow = $tables->fetch_array()) {
            $tableName = $tableRow[0];

            $columns = $conn->query("SHOW FULL COLUMNS FROM `$tableName`");
            if (!$columns) {
                error_log("Error fetching columns for $tableName: " . $conn->error);
                continue;
            }

            $schema = [];
            while ($col = $columns->fetch_assoc()) {
                $schema[$col["Field"]] = [
                    "Type" => $col["Type"],
                    "Collation" => $col["Collation"],
                    "Null" => $col["Null"],
                    "Key" => $col["Key"],
                    "Default" => $col["Default"],
                    "Extra" => $col["Extra"],
                    "Privileges" => $col["Privileges"],
                    "Comment" => $col["Comment"]
                ];
            }

            // Create JSON structure
            $tableSchema = [
                "table_name" => $tableName,
                "database_name" => $this->databaseName,
                "columns" => $schema,
                "generated_at" => date("Y-m-d H:i:s")
            ];

            // Write JSON file
            $jsonFile = rtrim($outputDir, "/") . "/" . $tableName . ".json";
            file_put_contents($jsonFile, json_encode($tableSchema, JSON_PRETTY_PRINT));

            echo "✅ Schema for table '$tableName' saved to $jsonFile\n";
        }

        $conn->close();
    }

}
