<?php

require_once "db.php";

/* =========================
   HEADERS (IMPORTANT)
========================= */
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

/* =========================
   INPUT
========================= */
$data = json_decode(file_get_contents("php://input"), true);

$username  = trim($data["username"] ?? "");
$password  = trim($data["password"] ?? "");
$serverIp  = trim($data["serverIp"] ?? "");

if (!$username || !$password || !$serverIp) {
    echo json_encode([
        "success" => false,
        "message" => "Missing required data"
    ]);
    exit;
}

/* =========================
   DB CONFIG
========================= */
$database   = "ServerLabDB";
$dbUser     = "sa";
$dbPassword = "2412";

/* =========================
   CONNECT
========================= */
$conn = getConnection($serverIp, $database, $dbUser, $dbPassword);

if (!$conn) {
    echo json_encode([
        "success" => false,
        "message" => "Database connection failed",
        "error" => sqlsrv_errors()
    ]);
    exit;
}

/* =========================
   QUERY
========================= */
$sql = "
SELECT TOP 1 *
FROM Technician_List
WHERE technician_user = ?
AND password = ?
";

$params = [$username, $password];

$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    echo json_encode([
        "success" => false,
        "message" => "Query failed",
        "error" => sqlsrv_errors()
    ]);
    exit;
}

$user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

sqlsrv_close($conn);

if (!$user) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid username or password"
    ]);
    exit;
}

/* =========================
   SUCCESS
========================= */
echo json_encode([
    "success" => true,
    "message" => "Login successful",
    "user" => [
        "id"   => $user["technician_id"] ?? null,
        "name" => $user["technician_user"]
    ]
]);