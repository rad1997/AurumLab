<?php

require_once "db.php";

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$work     = trim($data["work"] ?? "");
$serverIp = trim($data["serverIp"] ?? "");

if ($work === "" || $serverIp === "") {
    echo json_encode([
        "success" => false,
        "message" => "Missing required data"
    ]);
    exit;
}

$database   = "ServerLabDB";
$dbUser     = "sa";
$dbPassword = "2412";

$conn = getConnection(
    $serverIp,
    $database,
    $dbUser,
    $dbPassword
);

if (!$conn) {

    echo json_encode([
        "success" => false,
        "message" => "Database connection failed",
        "error" => sqlsrv_errors()
    ]);
    exit;
}

$sql = "
SELECT TOP 1 prices
FROM Technician_Work
WHERE work = ?
";

$params = [$work];

$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {

    echo json_encode([
        "success" => false,
        "message" => "Query failed",
        "error" => sqlsrv_errors()
    ]);
    exit;
}

$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

sqlsrv_close($conn);

if (!$row) {

    echo json_encode([
        "success" => false,
        "message" => "Work not found"
    ]);
    exit;
}

echo json_encode([
    "success" => true,
    "price" => (float)$row["prices"]
]);