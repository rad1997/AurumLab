<?php

require_once "db.php";

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$id       = trim($data["id"] ?? "");
$serverIp = trim($data["serverIp"] ?? "");

if ($id === "" || $serverIp === "") {
    echo json_encode([
        "success" => false,
        "message" => "Missing required data"
    ]);
    exit;
}

$database   = "ServerLabDB";
$dbUser     = "sa";
$dbPassword = "2412";

$conn = getConnection($serverIp, $database, $dbUser, $dbPassword);

if (!$conn) {
    echo json_encode([
        "success" => false,
        "message" => "Database connection failed",
        "error" => sqlsrv_errors()
    ]);
    exit;
}

$sql = "DELETE FROM treatment1 WHERE ID = ?";

$params = [$id];

$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    echo json_encode([
        "success" => false,
        "message" => "Delete query failed",
        "error" => sqlsrv_errors()
    ]);
    exit;
}

sqlsrv_close($conn);

echo json_encode([
    "success" => true,
    "message" => "Record deleted successfully"
]);