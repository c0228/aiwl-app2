<?php
require  __DIR__.'/../utils/utils.api.php';

class CountriesTest {
    private $genReport;
    private $apiPrefix;
    private $apiUrl;
    private $apiMethod;
    private $apiResponse;
    private $apiStatus;
    public function __construct() {
        $this->apiPrefix = $GLOBALS["API_PREFIX"];
        $this->genReport = $GLOBALS["GEN_REPORT"];
        $apiInfo = $GLOBALS["API_INFO"];
        $this->apiUrl = $apiInfo["countries"]["url"];
        $this->apiMethod = $apiInfo["countries"]["method"];
    }
    public function testExecute() {
        $url = $this->apiPrefix.$this->apiUrl;
        $this->apiResponse = callApi($url, $this->apiMethod);
        $this->apiStatus = (is_array($this->apiResponse) && count($this->apiResponse) > 0) ? 'PASSED' : 'FAILED';
        return $this->apiResponse;
    }
    public function __destruct() {
        $this->genReport->apiTestTitle([
                "title" => "Get List of Countries",
                "url" => $this->apiUrl,
                "method" => $this->apiMethod,
                "testCases" =>[
                    [
                        "title"=>"Test the response is providing Countries List or not",
                        "description"=>"We are hitting API and testing  the response is providing the countries list or not",
                        "url"=>$this->apiPrefix.$this->apiUrl,
                        "method"=>$this->apiMethod,
                        "inputRequestBody"=>"-",
                        "apiResponse"=>json_encode($this->apiResponse),
                        "testResult"=>"",
                        "status" => $this->apiStatus,
                        "comments"=>""
                    ]
                ]
            ]);
    }
}
?>