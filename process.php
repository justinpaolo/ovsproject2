<?php
session_start();
include("admin/include/conn.php");

if(isset($_POST['register_btn'])){

    $id_number = clean($_POST['id_number']);
    $firstname = clean($_POST['firstname']);
    $lastname = clean($_POST['lastname']);
    $gender = clean($_POST['gender']);
    $program = clean($_POST['program']);
    $year_level = clean($_POST['year_level']);
    $account = clean($_POST['account']);
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];
    $role = clean($_POST['role']); // Get role from form input

    try {
        // Check if id number already exists
        $check_query = "SELECT * FROM voters WHERE id_number = :id_number";
        $check_stmt = $conn->prepare($check_query);
        $check_stmt->execute(['id_number' => $id_number]);

        if($check_stmt->rowCount() > 0) {
            $_SESSION['status'] = "ID Number Already exists";
            header('Location: register.php');
            exit();
        }

        if($password !== $cpassword) {
            $_SESSION['status'] = "Passwords do not match";
            header('Location: register.php');
            exit();
        }

        $query = "INSERT INTO voters (id_number, firstname, lastname, gender, program, year_level, account, password, role, status, date) 
                  VALUES (:id_number, :firstname, :lastname, :gender, :program, :year_level, :account, :password, :role, 0, NOW())";

        $stmt = $conn->prepare($query);
        $stmt->execute([
            'id_number' => $id_number,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'gender' => $gender,
            'program' => $program,
            'year_level' => $year_level,
            'account' => $account,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => $role
        ]);

        $_SESSION['success'] = "Registered successfully!";
        header('Location: register.php');
        exit();
    } catch(PDOException $e) {
        $_SESSION['status'] = "Registration failed: " . $e->getMessage();
        header('Location: register.php');
        exit();
    }
}
?>