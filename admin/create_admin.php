<?php
include('include/conn.php');

// Admin credentials
$admin = 'admin'; // Replace with your desired admin username
$password = 'admin123'; // Replace with your desired admin password
$admin_name = 'Administrator'; // Replace with the admin's name

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

try {
    // Insert the admin credentials into the database
    $query = "INSERT INTO admin (admin, adminpassword, admin_name) VALUES (:admin, :adminpassword, :admin_name)";
    $stmt = $conn->prepare($query);
    $stmt->execute([
        ':admin' => $admin,
        ':adminpassword' => $hashed_password,
        ':admin_name' => $admin_name
    ]);

    echo "Admin user created successfully.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>