<?php

function getConnection(
    string $serverIp,
    string $database,
    string $username,
    string $password
) {
    $connectionInfo = [
        "Database" => $database,
        "UID" => $username,
        "PWD" => $password,
        "CharacterSet" => "UTF-8",
        "TrustServerCertificate" => true,
        "Encrypt" => false
    ];

    $conn = sqlsrv_connect($serverIp, $connectionInfo);

    if ($conn === false) {
        return false;
    }

    return $conn;
}