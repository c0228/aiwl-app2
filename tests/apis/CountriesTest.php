<?php
class CountriesTest {

    public function testExecute() {
        // Initial Setup
        $apiDetails = $GLOBALS["API_DETAILS"];
        $genReport = $GLOBALS["GEN_REPORT_OBJ"];

        // STEP #1: Get API Details
        $apiPrefix = $apiDetails["prefix"];
        $apiInfo = $apiDetails["info"]["countries"];
        $apiUrl = $apiInfo["url"];
        $apiMethod = $apiInfo["method"];
        $url = $apiPrefix.$apiUrl;

        // STEP #2: Call API with Details
        $apiResponse = callApi($url, $apiMethod);

        // STEP #3: Determine if API returned valid list
        $apiStatus = (is_array($apiResponse) && count($apiResponse) > 0)?'PASSED':'FAILED';

        // STEP #4: Generate Report
        $genReport->apiTestTitle([
            "title" => "Get List of Countries",
            "url" => $apiUrl,
            "method" => $apiMethod,
            "testCases" => [
                ["api" =>[
                    "title" => "Test the response is providing Countries List or not",
                    "description" => "We are hitting API and testing whether the response provides the list of countries.",
                    "url" => $apiPrefix . $apiUrl,
                    "method" => $apiMethod,
                    "inputRequestBody" => "-",
                    "apiResponse" => json_encode($apiResponse),
                    "expectedResult" => "",
                    "testResult" => "",
                    "status" => $apiStatus],
                 "step-logs" => [],
                 "comments" => ""
                ]
            ]
        ]);

        return $apiResponse;
    }
}
?>
