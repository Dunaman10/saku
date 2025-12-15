<?php
$conn = new mysqli("localhost", 
"root", 
"", 
"saku");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
