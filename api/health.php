<?php
require_once "db.php";
header("Content-Type: application/json");

$serverIp = $_POST["serverIp"] ?? "";
$database = $_POST["database"] ?? "";
$dbUser = $_POST["dbUser"] ?? "";
$dbPassword = $_POST["dbPassword"] ?? "";

$conn = getConnection($serverIp, $database, $dbUser, $dbPassword);

echo json_encode([
    "success" => $conn ? true : false,
    "message" => $conn ? "Connected" : "Failed"
]);