<?php
// require  __DIR__.'/../utils/utils.api.php';

class StatesTest {
    private $genReport;
    private $apiPrefix;
    private $apiUrls;
    private $apiMethod;
    private $apiResponses;
    private $testResults;

    public function __construct($countriesList) {
        $this->apiPrefix = $GLOBALS["API_PREFIX"];
        $this->genReport = $GLOBALS["GEN_REPORT"];
        $apiInfo = $GLOBALS["API_INFO"];
        
        $template = $apiInfo["states"]["url"];  // e.g. "get/{country}/data"
        $this->apiMethod = $apiInfo["states"]["method"]; // e.g. "GET"

        // Build full URLs dynamically based on countries list
        $this->apiUrls = array_map(function($country) use ($template) {
            return str_replace('{country}', $country, $template);
        }, $countriesList);

        $this->apiResponses = [];
        $this->testResults = [];
    }

    public function testExecute() {
        foreach ($this->apiUrls as $url) {
            $fullUrl = $this->apiPrefix . $url;
            $response = callApi($fullUrl, $this->apiMethod);

            // Determine pass/fail
            $status = (is_array($response) && count($response) > 0)
                ? 'PASSED'
                : 'FAILED';

            // Store response and result
            $this->apiResponses[$url] = $response;
            $this->testResults[] = [
                "title" => "Test States List for Country from URL: {$url}",
                "description" => "Testing whether the API returns a valid list of states for the given country.",
                "url" => $fullUrl,
                "method" => $this->apiMethod,
                "inputRequestBody" => "-",
                "apiResponse" => json_encode($response),
                "expectedResult" => "",
                "testResult" => "",
                "status" => $status,
                "comments" => ""
            ];
        }

        // Compile results into HTML report
        $this->genReport->apiTestTitle([
            "title" => "Get List of States per Country",
            "url" => "get/{country}/data",
            "method" => $this->apiMethod,
            "testCases" => $this->testResults
        ]);

        return $this->apiResponses;
    }
}
?>
