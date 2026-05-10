<?php
$host = "localhost";
$username = "mapping207_group19_user";
$password = "xamraf-wiZjem-5tecqy";
$database = "mapping207_group19_events";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>