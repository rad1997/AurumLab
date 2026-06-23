<?php

require_once "db.php";

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$appointmentId = trim($data["appointmentId"] ?? "");
$serverIp      = trim($data["serverIp"] ?? "");

if ($appointmentId === "" || $serverIp === "") {
    echo json_encode([
        "success" => false,
        "message" => "Missing required data",
        "received" => $data
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
    echo json_encode([
        "success" => false,
        "message" => "Query failed",
        "error" => sqlsrv_errors()
    ]);
    exit;
}

$rows = [];

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {

    if ($row["work_date"] instanceof DateTime) {
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
