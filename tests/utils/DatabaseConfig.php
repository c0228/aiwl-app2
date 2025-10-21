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
}
