<?php
 class TestCaseHelper {
    private $apiPrefix;
    private $apiUrl;
    private $apiMethod;
    public function __construct($apiUrl, $apiMethod){
        $this->apiPrefix = $GLOBALS["API_DETAILS"]["prefix"];
        $this->apiUrl = $apiUrl;
        $this->apiMethod = $apiMethod;
    }
    public function runAPI($testCase){
        $fullUrl = $this->apiPrefix . $this->apiUrl;
        $title = $testCase["title"];
        $desc = $testCase["desc"];
        $requestData = $testCase["data"];
        $expectedResultStatus = $testCase["expectedResult"]["status"];
        $expectedResultMessage = $testCase["expectedResult"]["message"];

        // Execute the API call
        $response = callApi($fullUrl, $this->apiMethod, $requestData);
        $status = 'FAILED';
        if (!is_array($response)) { $response = []; }
        if(isset($response['status']) && $response['status'] == $expectedResultStatus &&
            isset($response['message']) && $response['message'] === $expectedResultMessage){
            $status = 'PASSED';
        }

        $result =  [
            "title" => $title,
            "description" => $desc,
            "status" => $status,
            "url" => $fullUrl,
            "method" => $this->apiMethod,
            "inputRequestBody" => json_encode($requestData, JSON_PRETTY_PRINT),
            "apiResponse" => json_encode($response, JSON_PRETTY_PRINT),
            "expectedResult" => json_encode([
                "status" => $expectedResultStatus,
                "message" => $expectedResultMessage
            ]),
            "comments" => ""
        ];
        return $result;
    }
 }
?>