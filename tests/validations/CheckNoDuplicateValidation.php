<?php
 class CheckNoDuplicateValidation {
    private $apiPrefix;
    private $apiUrl;
    private $apiMethod;
    public function __construct($apiUrl, $apiMethod){
        $this->apiPrefix = $GLOBALS["API_DETAILS"]["prefix"];
        $this->apiUrl = $apiUrl;
        $this->apiMethod = $apiMethod;
    }
    public function validate( $apiAndDbTestCase, $testDetailsIndex ) {
        // 1) I will get existing Data from Database based on UniqueId mentioned & "createdBy" (starts with test-auito-%)
        // -------------------------------- CONFIRMS ONE COPY EXISTS ----------------------------------------------------
        // 2) I will hit an API with what I am passing to it (APITestData)
        // 3) Using UnqiueId, I will get the data again and confirms No Data got replicated from previous.
        // 4) If they tally, the test is PASSED
    }
 }
?>