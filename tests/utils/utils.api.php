<?php
/**
 * Call API with cURL
 */
function callApi($url, $method = 'GET', $data = null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        if ($data !== null) curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    $response = curl_exec($ch);
    if ($response === false) echo "CURL Error: " . curl_error($ch) . "\n";
    curl_close($ch);
    return json_decode($response, true);
}

/**
 * Validate DB value
 */
function validateDb($database, $table, $conditions, $expected) {
    $where = [];
    foreach ($conditions as $col => $val) {
        $where[] = "$col = '" . $database->escapeString($val) . "'";
    }
    $sql = "SELECT * FROM $table WHERE " . implode(' AND ', $where);
    $result = $database->getData($sql);
    return !empty($result) && array_intersect_assoc($expected, $result[0]) == $expected;
}