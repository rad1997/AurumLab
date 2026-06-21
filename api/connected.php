<?php

// IDE helper only (safe)
if (false) {
    require_once "ide-stubs.php";
}

require_once "db.php";

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, GET");

// Read input (JSON or form-data)
$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

if (!$data) {
    $data = $_POST;
}

$serverIp = $data["serverIp"] ?? "";

// Validate input
if (empty($serverIp)) {
    echo json_encode([
        "success" => false,
        "message" => "Missing serverIp"
    ]);
    exit;
}

// DB CONFIG
$database   = "ServerLabDB";
$dbUser     = "sa";
$dbPassword = "2412";

// Connect
$conn = getConnection($serverIp, $database, $dbUser, $dbPassword);

if (!$conn) {
    echo json_encode([
        "success" => false,
        "message" => "Connection failed",
        "serverIp" => $serverIp,
        "error" => print_r(sqlsrv_errors(), true)
    ]);
    exit;
}

// Test query
$sql = "SELECT GETDATE() AS server_time";
$stmt = sqlsrv_query($conn, $sql);

if ($stmt === false) {
    sqlsrv_close($conn);

    echo json_encode([
        "success" => false,
        "message" => "Query failed",
        "error" => print_r(sqlsrv_errors(), true)
    ]);
    exit;
}

// Fetch result safely
$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

if (!is_array($row)) {
    sqlsrv_close($conn);

    echo json_encode([
        "success" => false,
        "message" => "No data returned",
        "error" => print_r(sqlsrv_errors(), true)
    ]);
    exit;
}

// Close connection
sqlsrv_close($conn);

// Success response
echo json_encode([
    "success" => true,
    "message" => "Connected successfully",
    "serverIp" => $serverIp,
    "server_time" => $row["server_time"]
]);