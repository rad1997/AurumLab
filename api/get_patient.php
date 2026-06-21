<?php

require_once "db.php";

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST");

$data = json_decode(file_get_contents("php://input"), true);

$patientId = trim($data["patientId"] ?? "");
$serverIp  = trim($data["serverIp"] ?? "");

if (!$patientId || !$serverIp) {
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

$sql = "
SELECT TOP 1
    name,
    dentist,
    [date]
FROM Appointment
WHERE id = ?
";

$params = [$patientId];

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
        "message" => "Appointment not found"
    ]);
    exit;
}

echo json_encode([
    "success" => true,
    "name" => $row["name"] ?? "",
    "dentist" => $row["dentist"] ?? "",
    "receiveDate" => isset($row["date"])
        ? $row["date"]->format("Y-m-d")
        : ""
]);