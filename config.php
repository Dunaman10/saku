<?php
$conn = new mysqli(
    "localhost",
    "root",
    "dunaman",
    "saku"
);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
