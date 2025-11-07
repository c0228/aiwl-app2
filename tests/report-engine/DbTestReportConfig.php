<?php
class DbTestReportConfig {
 private function title($param){
    return '<div>
        <h4><b>Queries Executed 
            <span style="border:1px solid #ccc;padding:5px;background-color:#eee;">'.$param.'</span> 
            API Execution</b></h4><hr/>
    </div>';
 }
 private function dbTestTitle($obj){
    return '<div class="col-md-12">'.htmlspecialchars(!empty($obj["action"])?$obj["action"] : '-').'</div>';
 }
 private function dbTestElement($obj, $label, $param){
    return '<div class="col-md-12">
        <div><b>'.$label.': </b></div>
        <div class="card">
            <div class="card-body">'.htmlspecialchars(!empty($obj["query"])?$obj["query"] : '-').'</div>
        </div>
    </div>';
 }
 private function dbTestStatus($obj){
    $statusClass = ($obj["status"] ?? '') === "PASSED" ? "bg-success" : "bg-danger";
    return '<div class="col-md-12">
        <div><b>Status: </b> <span class="badge '.$statusClass.'">'.htmlspecialchars($obj["status"] ?? '-').'</span></div>
    </div>';
 }
 private function testDetails($obj, $param){
  $paramHtml = "";
  if (!empty($obj[$param]) && is_array($obj[$param])) {
    foreach ($obj[$param] as $i => $item) {
     $paramHtml .= '<div class="card mb-2">
                        <div class="card-body">
                            <div><span style="border:1px solid #ccc;padding:5px;font-size:16px;background-color:#ddd;"><b>Query #'.($i + 1).'B</b></span></div>
                            <div class="row" style="margin-top:15px;">'.$this->dbTestTitle($item)
                                .$this->dbTestElement($item, 'Query', 'query')
                                .$this->dbTestElement($item, 'Actual Result', 'actualResult')
                                .$this->dbTestElement($item, 'Expected Result', 'expectedResult')
                                .$this->dbTestStatus($item).'
                            </div>
                        </div>
                    </div>';
    }
  } else {
      $paramHtml = '<div>No "'.$param.'" queries executed.</div>';
  }
  return $paramHtml;
 }
 public function generate($dbTc) {
  $dbTestSection = "";
  if (!empty($dbTc) && is_array($dbTc)) {
        // Handle Before and After sections separately
            // Combine into full DB section
            $dbTestSection = '<div style="border:1px dashed #000; padding:15px;margin-top:15px;">
                <div align="center" class="row">
                    <div class="col-md-12 mt-2"><h3><b>DATABASE TEST RESULTS</b></h3></div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">'.$this->title("before").$this->testDetails($dbTc, "before").'</div>
                    <div class="col-md-6">'.$this->title("after").$this->testDetails($dbTc, "after").'</div>
                </div>
            </div>';
  }
  return $dbTestSection;
 }
}
?>