<?php
/***
 * 1) Load the JSON File : data/UserAccountsData.json
 * 2) From init -> database -> clean :
 *      a) Clean the Database Table "user_accounts_info" (with createdBy Column Reference by Default)
 * 3) From execute
 *      a) Start executing each Test Case
 */
class CreateUserAccountTest {
    private $databaseHelper;

    public function __construct(){
        $apiDetails = $GLOBALS["API_DETAILS"];
        $createUser = $apiDetails["info"]["createUser"];
        $apiUrl = $createUser["url"];
        $apiMethod = $createUser["method"];

        $this->databaseHelper = new DatabaseHelper($apiUrl, $apiMethod);
    }

    public function testExecute() {
        // STEP #1: Load TestJSONFile
        $testData = DataLoader::load( $GLOBALS["USER_ACCOUNT_TESTDATAFILE"] );

        // STEP #2: Using TestJSONFile
        // a) Run init -> database -> clean
            $this->runInit($testData["init"]);
        // b) Run execute -> testCases
            $this->runExecute($testData["execute"]);
    }

    private function runInit($initData){
        // STEP #1: Clean Database 
        $this->databaseHelper->cleanupTables($initData["database"]["clean"]);
    }

    private function runExecute($executeData){
        
        // STEP #1: Execute all defined test cases
        $testCasesList = array_keys( $executeData );
       // $testCaseHelper = new TestCaseHelper($apiUrl, $apiMethod);
        foreach($testCasesList as $testCaseName){
            $this->databaseHelper->executeApiAndDBTest($executeData[$testCaseName]);
            // API Test
            // $apiTest = $executeData[$testCaseName]["api"];
            // $databaseTest = $executeData[$testCaseName]["database"] ?? [];

            // Database Details:
            /* if(count($databaseTest)>0){
                $databaseTestTitle = $databaseTest["title"];
                $databaseTestDesc =  $databaseTest["desc"];
                $databaseTestDetails = $databaseTest["details"] ?? [];
                foreach($databaseTestDetails as $dbDetails){
                    $databaseTestTableName = $databaseTestDetails["tableName"] ?? "";
                    switch($dbDetails["expectedResult"]){
                        case "CHECK_NO_EMPTY":
                            $defaultValFields = $databaseTestDetails["defaultValFields"] ?? [];
                            $databaseHelper->checkNoEmpty($databaseTest, $apiTest);
                    }
                    
                }
            } */
            
           //  $apiTestResponse = $testCaseHelper->runAPI( $apiTest );

            // Database Test
            

            
           // $databaseHelper->testInDatabase($databaseTest, $apiTest["data"] ); // Need to be Write
        }

        // Generate Report to be set (By seeing countriesAndStatesAPI)
    }

}
?>