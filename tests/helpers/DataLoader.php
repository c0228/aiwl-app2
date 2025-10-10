<?php
/**
 * Utility: DataLoader
 * --------------------------------
 * Loads test case data from JSON files.
 */
class DataLoader {
    public static function load($filePath) {
        if (!file_exists($filePath)) {
            throw new Exception("❌ JSON data file not found: $filePath");
        }
        $json = file_get_contents($filePath);
        $data = json_decode($json, true);
        if ($data === null) {
            throw new Exception("❌ Invalid JSON in file: $filePath");
        }
        return $data;
    }
}
?>
