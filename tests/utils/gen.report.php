<?php
 class GenerateReport {
    private $reportFile;
    private $apiIndex = 1;
    private $testCaseIndex = 1;
    private $apiDisplayData = '';
    public function __construct($reportFile) {
        $this->reportFile = $reportFile;
    }

    function apiTestTitle($data){
      $testCaseData = '<div class="mt-4">
            <h4>
                <b>' . $this->apiIndex . '. ' . $data["title"] . 
                ' <span class="badge bg-primary">' . $data["url"] . '</span></b>
                <span class="float-end badge bg-success">' . $data["method"] . '</span>
                <hr/>
            </h4>
        </div>';
     if (!empty($data["testCases"]) && is_array($data["testCases"])) {
        
        foreach ($data["testCases"] as $tc) {
             $index = $this->testCaseIndex;
             $statusBgColor = ($tc["status"]=='PASSED')?'bg-success':'bg-danger';
             $testCaseData .= '<div class="list-group mb-2">
                <div id="test-case-'.$index.'" class="list-group-item header" data-bs-toggle="collapse" 
                    data-bs-target="#test-case-'.$index.'-toggle" 
                    onClick="javascript:toggleTestCase('.$index.')">
                    <div><b>TEST CASE #'.$index.': '.$tc["title"].'</b>
                    <span class="float-end">
                    <span class="badge '.$statusBgColor.'"><b>'.$tc["status"].'</b></span>
                    <i class="fa fa-angle-double-down" aria-hidden="true" style="font-size:19px;"></i>
                    </span>
                    </div>
                </div>
                <div id="test-case-'.$index.'-toggle" class="list-group-item collapse">
                    <div class="row">
                        <div class="col-md-12 mt-2"><b>Description:</b> '.$tc["description"].'</div>
                        <div class="col-md-12 mt-2"><b>URL:</b> <code class="code"><b>'.$tc["url"].'</b></code></div>
                        <div class="col-md-12 mt-2"><b>Method:</b> <span class="badge bg-success">'.$tc["method"].'</span> </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mt-2">
                            <div><b>Request Body:</b></div>
                            <div class="card code">
                                <div class="card-body">
                                    '.$tc["inputRequestBody"].'
                                </div><!--/.card-body -->
                            </div><!--/.card -->
                        </div><!--/.col-md-4 -->
                        <div class="col-md-4 mt-2">
                            <div><b>API Response:</b></div>
                            <div class="card code">
                                <div class="card-body">
                                    '.$tc["apiResponse"].'
                                </div><!--/.card-body -->
                            </div><!--/.card -->
                        </div><!--/.col-md-4 -->
                        <div class="col-md-4 mt-2">
                            <div><b>Test Result:</b></div>
                            <div class="card">
                                <div class="card-body">
                                    '.$tc["testResult"].'
                                </div><!--/.card-body -->
                            </div><!--/.card -->
                        </div><!--/.col-md-4 -->
                    </div><!--/.row -->
                    <div class="row">
                        <div class="col-md-12 mt-2">
                            <div><b>Comments:</b></div>
                            <div class="card">
                                <div class="card-body">
                                    '.$tc["comments"].'
                                </div><!--/.card-body -->
                            </div><!--/.card -->
                        </div>
                    </div><!--/.row -->
                </div><!--/.list-group-item -->
            </div><!--/.list-group -->';
            $this->testCaseIndex++;
        }
       
     }
     $this->apiDisplayData .= $testCaseData;
     $this->apiIndex++;
    }

    function cleanHtml($html) {
        // Trim leading/trailing spaces from each line
        $lines = explode("\n", $html);
        $lines = array_map('trim', $lines);
        // Remove empty lines
        $lines = array_filter($lines, fn($line) => $line !== '');
        // Join lines with a single newline
        $html = implode("\n", $lines);
        return $html;
    }

    public function __destruct() {
      $html ='
            <!DOCTYPE html>
            <html lang="en">
            <head>
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
            <style>
                body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; }
                .code { font-weight:bold;background-color: #f5f5f5;padding: 5px 10px;border: 1px solid #db3684;color:#db3684;border-radius: 8px; }
                .header { background-color: #eee;cursor:pointer; }
            </style>
            <script>
            function toggleTestCase(index){ 
                $("#test-case-"+index+"-toggle").collapse("toggle");
                $("#test-case-"+index).find("i").toggleClass("fa-angle-double-up fa-angle-double-down"); 
            }
            </script>
            </head>
            <body>
                <div class="container-fluid mt-4">
                    <div align="center"><h4><b>API TEST RESULTS</b></h4></div>
                    '.$this->apiDisplayData.'
                </div>
            </body>
            </html>';
     $html = $this->cleanHtml($html);
     file_put_contents($this->reportFile, $html);
    }
 }
/*
 $data1 = array();
 $data1["title"] = "TEST Title 1";
 $data1["url"] = "TEST URL 1";
 $data1["method"] = "TEST Method 1";
 $data = array();
 $data[0] = $data1;
 $generateReport = new GenerateReport("new-gen-report.html");
 $generateReport->apiTestTitle([
    "title" => "TEST Title 1",
    "url" => "TEST URL 1",
    "method" => "TEST Method 1",
    "testCases" =>[
        [
            "title"=>"",
            "description"=>"",
            "url"=>"",
            "method"=>"",
            "inputRequestBody"=>"",
            "apiResponse"=>"",
            "testResult"=>"",
            "comments"=>""
        ],
        [
            "title"=>"",
            "description"=>"",
            "url"=>"",
            "method"=>"",
            "inputRequestBody"=>"",
            "apiResponse"=>"",
            "testResult"=>"",
            "comments"=>""
        ]
    ]
]);
$generateReport->apiTestTitle([
    "title" => "TEST Title 2",
    "url" => "TEST URL 2",
    "method" => "TEST Method 2",
    "testCases" =>[
        [
            "title"=>"",
            "description"=>"",
            "url"=>"",
            "method"=>"",
            "inputRequestBody"=>"",
            "apiResponse"=>"",
            "testResult"=>"",
            "comments"=>""
        ]
    ]
]); */
?>