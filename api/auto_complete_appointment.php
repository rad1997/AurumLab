<?php

require_once "db.php";

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$appointmentid = trim($data["appointmentid"] ?? "");
$serverIp      = trim($data["serverIp"] ?? "");

if ($appointmentid === "" || $serverIp === "") {
    echo json_encode([
        "success" => false,
        "message" => "Missing data"
    ]);
    exit;
}

$conn = getConnection($serverIp, "ServerLabDB", "sa", "2412");

if (!$conn) {
    echo json_encode([
        "success" => false,
        "message" => "DB connection failed",
        "error" => sqlsrv_errors()
    ]);
    exit;
}

$sqlCheck = "
SELECT 
    COUNT(*) AS totalRows,
    SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) AS completedRows
FROM treatment1
WHERE appointmentid = ?
";

$stmt = sqlsrv_query($conn, $sqlCheck, [$appointmentid]);

if ($stmt === false) {
    echo json_encode([
        "success" => false,
        "message" => "Query failed",
        "error" => sqlsrv_errors()
    ]);
    exit;
}

$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

$total = intval($row["totalRows"] ?? 0);
$done  = intval($row["completedRows"] ?? 0);

// IMPORTANT FIX: allow update even if 0 rows? (your choice)
// here we keep strict
if ($total > 0 && $total === $done) {

    $sqlUpdate = "
    UPDATE Appointment
    SET appointment = 'Complete'
    WHERE id = ?
    ";

    $updateStmt = sqlsrv_query($conn, $sqlUpdate, [$appointmentid]);

    if ($updateStmt === false) {
        echo json_encode([
            "success" => false,
            "message" => "Appointment update failed",
            "error" => sqlsrv_errors()
        ]);
        exit;
    }

    echo json_encode([
        "success" => true,
        "message" => "Appointment marked as Complete"
    ]);

} else {

    echo json_encode([
        "success" => true,
        "message" => "Not all treatments completed",
        "total" => $total,
        "completed" => $done
    ]);
}

sqlsrv_close($conn);