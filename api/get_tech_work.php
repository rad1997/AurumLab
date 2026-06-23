<?php

require_once "db.php";

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

// Preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Read JSON safely
$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Invalid JSON format"
    ]);
    exit;
}

$appointmentId = trim($data["appointmentId"] ?? "");
$serverIp      = trim($data["serverIp"] ?? "");

if ($appointmentId === "" || $serverIp === "") {
    http_response_code(400);
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
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Database connection failed"
    ]);
    exit;
}

$sql = "
SELECT
    ID,
    tech_name,
    tech_work,
    unit,
    tech_prices,
    work_date
FROM treatment1
WHERE appointmentid = ?
ORDER BY work_date DESC
";

$params = [$appointmentId];

$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Query execution failed"
    ]);
    exit;
}

$rows = [];

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {

    if (!empty($row["work_date"]) && $row["work_date"] instanceof DateTime) {
        $row["work_date"] = $row["work_date"]->format("Y-m-d");
    }

    $rows[] = $row;
}

sqlsrv_close($conn);

echo json_encode([
    "success" => true,
    "count"   => count($rows),
    "rows"    => $rows
]);
