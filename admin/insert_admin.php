<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ovsproject";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Hash the password
$admin_username = "admin";
$admin_password = "admin123";
$hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);

// Insert or update admin details
$sql = "INSERT INTO admin (admin, password) VALUES (?, ?) ON DUPLICATE KEY UPDATE password=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $admin_username, $hashed_password, $hashed_password);

if ($stmt->execute()) {
    echo "Admin account created/updated successfully!";
} else {
    echo "Error: " . $conn->error;
}

$stmt->close();
$conn->close();
?>