<?php
 class CheckDataUpdateValidation {
    private $apiPrefix;
    private $apiUrl;
    private $apiMethod;
    public function __construct($apiUrl, $apiMethod){
        $this->apiPrefix = $GLOBALS["API_DETAILS"]["prefix"];
        $this->apiUrl = $apiUrl;
        $this->apiMethod = $apiMethod;
    }
    public function validate( $apiAndDbTestCase, $testDetailsIndex ) {
        // 1) I will get existing Data from Database based on uniqueId mentioned & "createdBy" (starts with test-auto-%)
        // 2) I will hit an API with what I am passing to it (APITestData)
        // 3) Using uniqueId, I will get the updated Dtaa and compares it with existing one.
        // If they tally, the test is passed.
    }
 }
?>