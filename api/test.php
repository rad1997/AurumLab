<?php

header("Content-Type: application/json");

// Safe constant fallback for IDE + runtime safety
if (!defined('SQLSRV_FETCH_ASSOC')) {
    define('SQLSRV_FETCH_ASSOC', 2);
}

$server = "192.168.1.66,1433";

$connectionInfo = [
    "Database" => "ServerLabDB",
    "UID" => "sa",
    "PWD" => "2412",
    "CharacterSet" => "UTF-8",
    "TrustServerCertificate" => true,
    "Encrypt" => false
];

$conn = sqlsrv_connect($server, $connectionInfo);

if ($conn) {

    $sql = "SELECT 1 AS test";
    $stmt = sqlsrv_query($conn, $sql);

    $result = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "message" => "Database connection OK",
        "test_result" => $result
    ]);

    sqlsrv_close($conn);

} else {

    echo json_encode([
        "success" => false,
        "message" => "Connection failed",
        "error" => sqlsrv_errors()
    ]);
}