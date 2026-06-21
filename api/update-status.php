<?php
require_once "db.php";
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

$conn = getConnection(
    $data["serverIp"],
    $data["database"],
    $data["dbUser"],
    $data["dbPassword"]
);

$stmt = $conn->prepare("
UPDATE Appointment
SET appointment = ?
WHERE id2 = ?
");

$stmt->execute([
    $data["status"],
    $data["id2"]
]);

echo json_encode(["success" => true]);