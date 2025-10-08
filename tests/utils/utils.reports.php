<?php
class HTMLReport {
    private $reportFile;
    private $templateFile;
    private $rows = [];

    public function __construct($reportFile, $templateFile) {
        $this->reportFile = $reportFile;
        $this->templateFile = $templateFile;
        echo "Report will be saved to: {$this->reportFile}<br>";
    }

    // Add test result rows
    public function addRow($testName, $method, $url, $expected, $actual, $result, $response, $error) {
        $resultClass = ($result === 'PASS') ? 'text-success' : 'text-danger';
        $resultTable =  ($result === 'PASS') ? 'table-success' : 'table-danger';
        $row = "<tr class='{$resultTable}'>
                    <td>{$testName}</td>
                    <td>{$method}</td>
                    <td>{$url}</td>
                    <td>{$expected}</td>
                    <td>{$actual}</td>
                    <td class='{$resultClass}'>{$result}</td>
                    <td><pre>{$response}</pre></td>
                    <td>{$error}</td>
                </tr>";
        $this->rows[] = $row;
    }

    // Destructor — writes file when object ends
    public function __destruct() {
        if (!file_exists($this->templateFile)) {
            echo "Error: Template file not found!";
            return;
        }

        $template = file_get_contents($this->templateFile);
        $rowsHTML = implode("\n", $this->rows);
        $finalHTML = str_replace('{{ROWS}}', $rowsHTML, $template);

        file_put_contents($this->reportFile, $finalHTML);
        echo "Report generated successfully: {$this->reportFile}<br>";
        readfile($this->reportFile);
    }
}

// Example usage
$report = new HTMLReport("testReport.html", __DIR__ . "/../templates/reports.template.html");

$report->addRow("Get Countries", "GET", "https://api.example.com/countries", 200, 200, "PASS", '{"status":"ok"}', "None");
$report->addRow("Invalid API", "GET", "https://api.example.com/wrong", 404, 500, "FAIL", '{"error":"server"}', "Timeout");
?>
