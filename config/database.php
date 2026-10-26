<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| AUAF Event Management - Database Configuration
|--------------------------------------------------------------------------
| Update the database name, username, and password when you create
| your MySQL database.
*/

$host = 'localhost';
$port = '3306';
$dbname = 'auaf_event_management';
$username = 'root';
$password = '';

$charset = 'utf8mb4';

$dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    error_log('Database connection failed: ' . $e->getMessage());

    http_response_code(500);
    exit('Unable to connect to the database. Please check the database configuration.');
}
