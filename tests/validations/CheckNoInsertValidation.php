<?php
 class CheckNoInsertValidation {
    private $apiPrefix;
    private $apiUrl;
    private $apiMethod;
    public function __construct($apiUrl, $apiMethod){
        $this->apiPrefix = $GLOBALS["API_DETAILS"]["prefix"];
        $this->apiUrl = $apiUrl;
        $this->apiMethod = $apiMethod;
    }
    public function validate( $apiAndDbTestCase, $testDetailsIndex ) {
        // 1) I will hit an API with what I am passing to it with a "createdBy" (APITestData)
        // 2) With same "createdBy", I will check in Database
        // 3) If it not exists, then the test is PASSED
    }
 }
?>