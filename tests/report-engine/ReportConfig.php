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
            $apiTc = $tc["api"];
            $dbTc = $tc["database"];
             $index = $this->testCaseIndex;
             $statusBgColor = ($apiTc["status"]=='PASSED')?'bg-success':'bg-danger';

             $stepLogsHtml = "";
             if (!empty($tc["step-logs"]) && is_array($tc["step-logs"])) {
                foreach ($tc["step-logs"] as $log) {
                    $stepLogsHtml .= '<div> ' 
                        .htmlspecialchars($log["step"]) . ' - <b>' 
                        .htmlspecialchars($log["status"]) . '</b></div>';
                }
             } else {
                    $stepLogsHtml = "<div>No step logs available.</div>";
             }
             //  Database Test Section (display only if $dbTc exists)
             $dbTestReportConfig = new DbTestReportConfig();
             $dbTestSection = $dbTestReportConfig->generate($dbTc);

             $testCaseData .= '<div class="list-group mb-2">
                <div id="test-case-'.$index.'" class="list-group-item header" data-bs-toggle="collapse" 
                    data-bs-target="#test-case-'.$index.'-toggle" 
                    onClick="javascript:toggleTestCase('.$index.')">
                    <div><b>TEST CASE #'.$index.': '.$apiTc["title"].'</b>
                    <span class="float-end">
                    <span class="badge '.$statusBgColor.'"><b>'.$apiTc["status"].'</b></span>
                    <i class="fa fa-angle-double-down" aria-hidden="true" style="font-size:19px;"></i>
                    </span>
                    </div>
                </div>
                <div id="test-case-'.$index.'-toggle" class="list-group-item collapse">
                    <div class="row">
                        <div class="col-md-12 mt-2"><b>Description:</b> '.$apiTc["description"].'</div>
                    </div>

                    <div style="border:1px dashed #000; padding:15px;margin-top:15px;">
                    <!-- -->
                    <div align="center" class="row">
                        <div class="col-md-12 mt-2"><h3><b>API TEST RESULTS</b></h3></div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 mt-2"><b>URL:</b> <code class="code"><b>'.$apiTc["url"].'</b></code></div>
                        <div class="col-md-12 mt-2"><b>Method:</b> <span class="badge bg-success">'.$apiTc["method"].'</span> </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mt-2">
                            <div><b>Request Body:</b></div>
                            <div class="card code">
                                <div class="card-body">
                                    '.$apiTc["inputRequestBody"].'
                                </div><!--/.card-body -->
                            </div><!--/.card -->
                        </div><!--/.col-md-4 -->
                        <div class="col-md-4 mt-2">
                            <div><b>API Response:</b></div>
                            <div class="card code">
                                <div class="card-body">
                                    '.$apiTc["apiResponse"].'
                                </div><!--/.card-body -->
                            </div><!--/.card -->
                        </div><!--/.col-md-4 -->
                        <div class="col-md-4 mt-2">
                            <div><b>Expected Response:</b></div>
                            <div class="card code">
                                <div class="card-body">
                                    '.$apiTc["expectedResult"].'
                                </div><!--/.card-body -->
                            </div><!--/.card -->
                        </div><!--/.col-md-4 -->
                    </div><!--/.row -->
                    <!-- -->
                    </div>'
                    .$dbTestSection.
                    '<div class="row">
                        <div class="col-md-6 mt-2">
                            <div><b>Step Logs:</b></div>
                            <div class="card code">
                                <div class="card-body">
                                    '.$stepLogsHtml.'
                                </div><!--/.card-body -->
                            </div><!--/.card -->
                        </div>
                        <div class="col-md-6 mt-2">
                            <div><b>Comments:</b></div>
                            <div class="card code">
                                <div class="card-body">
                                    '.$apiTc["comments"].'
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
?>