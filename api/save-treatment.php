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
INSERT INTO treatment1
(appointmentid, dentist, patient_name, tech_name, tech_work,
 work_type, unit, tech_prices, work_date, status)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->execute([
    $data["appointmentid"],
    $data["dentist"],
    $data["patient_name"],
    $data["tech_name"],
    $data["tech_work"],
    $data["work_type"],
    $data["unit"],
    $data["tech_prices"],
    $data["work_date"],
    $data["status"]
]);

echo json_encode(["success" => true]);