<?php
// config/conn.php
require_once __DIR__ . '/config.php';

mysqli_report(MYSQLI_REPORT_OFF);
date_default_timezone_set(APP_TIMEZONE);

$mysqli = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($mysqli->connect_errno) {
    http_response_code(500);
    echo "Database connection failed.";
    exit;
}

$mysqli->set_charset('utf8mb4');
?>
