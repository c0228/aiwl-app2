<?php
/***
 * 1) Load the JSON File : data/UserAccountsData.json
 * 2) From init -> database -> clean :
 *      a) Clean the Database Table "user_accounts_info" (with createdBy Column Reference by Default)
 * 3) From execute
 *      a) Start executing each Test Case
 */
class CreateUserAccountTest {
 
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
        $databaseHelper = new DatabaseHelper();
        $databaseHelper->cleanupTables($initData["database"]["clean"]);
    }

    private function runExecute($executeData){
        $apiDetails = $GLOBALS["API_DETAILS"];
        $createUser = $apiDetails["info"]["createUser"];
        $apiUrl = $createUser["url"];
        $apiMethod = $createUser["method"];
        // STEP #1: Execute all defined test cases
        $testCasesList = array_keys( $executeData );
        $testCaseHelper = new TestCaseHelper($apiUrl, $apiMethod);
        foreach($testCasesList as $testCaseName){
            // API Test
            $apiTest = $executeData[$testCaseName]["api"];
            $apiTestResponse = $testCaseHelper->runAPI( $apiTest );

            // Database Test
            $databaseTest = $executeData[$testCaseName]["database"] ?? [];
            $databaseHelper = new DatabaseHelper();
            $databaseHelper->testInDatabase($databaseTest, $apiTest["data"] ); // Need to be Write
        }

        // Generate Report to be set (By seeing countriesAndStatesAPI)
    }

}
?>