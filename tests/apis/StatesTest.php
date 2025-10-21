<?php

class StatesTest {

    public function testExecute($countriesList) {
        if (!is_array($countriesList) || empty($countriesList)) {
            throw new InvalidArgumentException("countriesList must be a non-empty array");
        } else {
        // Initial Setup
        $apiDetails = $GLOBALS["API_DETAILS"];
        $genReport = $GLOBALS["GEN_REPORT_OBJ"];

        // STEP #1: Get API Details
        $apiPrefix = $apiDetails["prefix"];
        $apiInfo = $apiDetails["info"]["states"];
        $template = $apiInfo["url"];
        $apiUrls = array_map(function($country) use ($template) {
                    return str_replace('{country}', $country, $template);
                  }, $countriesList);
        $apiMethod = $apiInfo["method"];

        // STEP #2: Execute API for each Country and test the Responses
        foreach ($apiUrls as $url) {
            $fullUrl = $apiPrefix . $url;
            $apiResponse = callApi($fullUrl, $apiMethod);
            // Determine pass/fail
            $apiStatus = (is_array($apiResponse) && count($apiResponse) > 0)? 'PASSED': 'FAILED';
            // Store response and result
            $apiResponses[$url] = $apiResponse;
            $testResults[] = [
                "title" => "Test States List for Country from URL: {$url}",
                "description" => "Testing whether the API returns a valid list of states for the given country.",
                "url" => $fullUrl,
                "method" => $apiMethod,
                "inputRequestBody" => "-",
                "apiResponse" => json_encode($apiResponse),
                "expectedResult" => "",
                "testResult" => "",
                "status" => $apiStatus,
                "step-logs" => "",
                "comments" => ""
            ];
        }

        // STEP #3: Compile results into HTML report
        $genReport->apiTestTitle([
            "title" => "Get List of States per Country",
            "url" => "get/{country}/data",
            "method" => $apiMethod,
            "testCases" => $testResults
        ]);

        return $apiResponses;
      }
    }
}
?>
