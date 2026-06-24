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

// ================================
// GET DATA
// ================================
$appointmentid = trim($data["appointmentid"] ?? "");
$dentist       = trim($data["dentist"] ?? "");
$patient_name  = trim($data["patient_name"] ?? "");
$tech_name     = trim($data["tech_name"] ?? "");
$tech_work     = trim($data["tech_work"] ?? "");
$work_type     = trim($data["work_type"] ?? "");
$unit          = intval($data["unit"] ?? 0);
$status        = trim($data["status"] ?? "InProgress");
$tech_prices   = floatval($data["tech_prices"] ?? 0);
$work_date     = trim($data["work_date"] ?? "");
$serverIp      = trim($data["serverIp"] ?? "");

// ================================
// VALIDATION
// ================================
if (
    $appointmentid === "" ||
    $tech_name === "" ||
    $tech_work === "" ||
    $unit <= 0 ||
    $serverIp === ""
) {
    echo json_encode([
        "success" => false,
        "message" => "Missing required data"
    ]);
    exit;
}

// ================================
// DB CONNECTION
// ================================
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

// ================================
// INSERT QUERY
// ================================
$sql = "
INSERT INTO treatment1
(
    appointmentid,
    dentist,
    patient_name,
    tech_name,
    tech_work,
    work_type,
    unit,
    status,
    tech_prices,
    work_date
)
VALUES
(
    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
)
";

$params = [
    $appointmentid,
    $dentist,
    $patient_name,
    $tech_name,
    $tech_work,
    $work_type,
    $unit,
    $status,
    $tech_prices,
    $work_date
];

$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {

    echo json_encode([
        "success" => false,
        "message" => "Insert failed",
        "error" => sqlsrv_errors()
    ]);
    exit;
}

sqlsrv_close($conn);

echo json_encode([
    "success" => true,
    "message" => "Preparation saved successfully"
]);