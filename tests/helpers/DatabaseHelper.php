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
}
?>