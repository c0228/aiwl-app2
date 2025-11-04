<?php

class DatabaseConfig {
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
        $databaseQueryBuilder = new DatabaseQueryBuilder();
        $sql = $databaseQueryBuilder->selectQuery($table, $conditions);
        // Reuse getJSONData() instead of doing connection/query manually
        $jsonData = $this->getJSONData($sql);
        $rows = json_decode($jsonData, true); // convert JSON to array
        // If no data returned
        if (empty($rows)) { return false; }
        // Loop through all rows to find a match by key-value pairs
        foreach ($rows as $row) {
            $match = true; // assume this row matches until proven otherwise

            foreach ($expected as $key => $value) {
                // if the key doesn’t exist or value mismatch → mark false
                if (!array_key_exists($key, $row) || $row[$key] != $value) {
                    $match = false;
                    break;
                }
            }
            // if all expected key-value pairs matched, we’re done 
            if ($match) { return true; }
        }
        // none of the rows matched the expected data
        return false;
    }

        /**
     * Delete records from a table based on given conditions.
     * 
     * @param string $table Table name
     * @param array $conditions Key-value pairs for WHERE clause
     * @return bool True if delete successful, false otherwise
     */
    function deleteFromTable($table, $conditions) {
        $conditionsStr = implode(' AND ', array_map(
            fn($key, $value) => "$key = '" . addslashes($value) . "'",
            array_keys($conditions), $conditions ));
        $databaseQueryBuilder = new DatabaseQueryBuilder();
        $sql = $databaseQueryBuilder->deleteQuery($table, $conditionsStr);
        return $this->deleteData($sql);
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
            $columnNames = [];
            while ($col = $columns->fetch_assoc()) {
                $columnNames[] = $col["Field"];
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
                "columns" => $columnNames,
                "columnDetails" => $schema,
                "generated_at" => date("Y-m-d H:i:s")
            ];

            // Write JSON file
            $jsonFile = rtrim($outputDir, "/") . "/" . $tableName . ".json";
            file_put_contents($jsonFile, json_encode($tableSchema, JSON_PRETTY_PRINT));

            echo "✅ Schema for table '$tableName' saved to $jsonFile\n";
        }

        $conn->close();
    }

    function getJSONData($sql) {
        $db=new DatabaseConfig($this->serverName,$this->databaseName,$this->userName,$this->password);
        $conn = $db->dbinteraction();
        $result = mysqli_query($conn, $sql); 
        $json="";
            if (!$result) {   
                 die("Invalid query: " . mysqli_error($conn)); 
           //      $this->logger->error("Query(Status-Invalid) : ".$sql); 
            }
            else {
                $rows= array();
        
                while($row = $result->fetch_assoc()) {
                    $rows[] = $row;
                 }
                 
                $json = json_encode($rows);
            }
         
        mysqli_free_result($result); 
        $conn->close();
        return $json;
    }

    function addupdateData($sql) {
       $status="Error";
       $db=new DatabaseConfig($this->serverName,$this->databaseName,$this->userName,$this->password);
       $conn = $db->dbinteraction();
       if ($conn->multi_query($sql) === true) { $status="Success";}
       $affectedRows = $conn->affected_rows;
       $conn->close();
       return ["status"=>$status, "affectedRows" =>$affectedRows];
    }
    
	function deleteData($sql) {
		$status='Error';
		$db=new DatabaseConfig($this->serverName,$this->databaseName,$this->userName,$this->password);
		$conn = $db->dbinteraction();
		if ($conn->query($sql) === TRUE) { $status='Success'; } 
        $affectedRows = $conn->affected_rows;
        $conn->close();
	    return ["status"=>$status, "affectedRows" =>$affectedRows];
	}
}
