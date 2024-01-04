<?php 

$conn = new mysqli('localhost', 'root', '', 'proje_yonetim_sistemi');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

