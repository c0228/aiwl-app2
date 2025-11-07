<?php
class CheckNoEmptyValidation {
    private $apiPrefix;
    private $apiUrl;
    private $apiMethod;
    public function __construct($apiUrl, $apiMethod){
        $this->apiPrefix = $GLOBALS["API_DETAILS"]["prefix"];
        $this->apiUrl = $apiUrl;
        $this->apiMethod = $apiMethod;
    }

    private function isRowHasEmptyFields($dbTableName){
        // Get All Data from Table Schemas File
        $databaseConfig = $GLOBALS["DB_CONN"];
        $databaseQueryBuilder = new DatabaseQueryBuilder();
        $conditions = $databaseQueryBuilder->buildEmptyDataCheckQuery($dbTableName);
        return $databaseConfig->validateDb($dbTableName, $conditions, []);
    }

    // NOTE: By Default, we will pass createdBy to all of the Tests.
    public function validate( $apiAndDbTestCase, $testDetailsIndex ){
        // 1) checks in the table, do we have any empty data inserted into it.
        // 2) I will hit an API with empty Data (API Test Data)
        // 3) Again, checks in the table, do we have any empty data inserted into it.
        // 4) If empty field exists, test is Failed
        
        // 1) checks in the table, do we have any empty data inserted into it.
        $dbTitle = $apiAndDbTestCase["database"]["title"];
        $dbDesc = $apiAndDbTestCase["database"]["desc"];
        $dbTableName = $apiAndDbTestCase["database"]["details"][$testDetailsIndex]["tableName"];
        $dbExpectedResult = $apiAndDbTestCase["database"]["details"][$testDetailsIndex]["expectedResult"];

        // Get All Data from Table Schemas File
        $status = $this->isRowHasEmptyFields($dbTableName);
        echo "IS_EMPTY: ".$status;
        
        // 2) I will hit an API with empty Data (API Test Data)
        $testCaseHelper = new TestCaseHelper($this->apiUrl, $this->apiMethod);
        $result = $testCaseHelper->runAPI($apiAndDbTestCase["api"]);
        print_r($result);

        // Again check, does anything inserted with Empty Fields
        $status = $this->isRowHasEmptyFields($dbTableName);
        echo "IS_EMPTY: ".$status;
        

        // GET EXPECTED RESULTS in "database"->"details"
    }
}
?>