<?php

echo "sqlsrv_connect: ";
var_dump(function_exists('sqlsrv_connect'));

echo "<br>";

echo "pdo_sqlsrv: ";
var_dump(in_array('sqlsrv', PDO::getAvailableDrivers()));