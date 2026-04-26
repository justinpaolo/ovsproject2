<?php
session_start();
include('admin/include/conn.php');

// Define the clean() function for XSS protection only if it doesn't already exist
if (!function_exists('clean')) {
    function clean($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return $data;
    }
}

if (isset($_POST['login_btn'])) {
    $id_number = clean($_POST['id_number']);
    $password = $_POST['password']; // Passwords should not be sanitized
    $role = clean($_POST['role']);

    try {
        // Check if the user exists
        $query = "SELECT * FROM voters WHERE id_number = ? AND account = ?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$id_number, $role]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Check if the voter's account is validated
            if ($user['validation_status'] != 'accepted') {
                $_SESSION['status'] = htmlspecialchars("Your account is not validated yet. Wait for the admin to validate your account.", ENT_QUOTES, 'UTF-8');
                header("Location: index.php");
                exit();
            }

            // Check if the voter has already voted
            // if ($user['status'] == 1) {
            //     $_SESSION['status'] = htmlspecialchars("You have already voted. You cannot log in again.", ENT_QUOTES, 'UTF-8');
            //     header("Location: index.php");
            //     exit();
            // }

            // Set session variables
            $_SESSION['voter_id'] = $user['voter_id'];
            $_SESSION['id_number'] = $user['id_number'];
            $_SESSION['role'] = $user['account'];

            // Check voting status
            $query = "SELECT voting_status FROM settings WHERE id = 1";
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $voting_status = $stmt->fetchColumn();

            if ($voting_status == 0) {
                echo "<script>
                        alert('Voting is currently Closed.');
                        window.location.href = 'dashboard/voting_closed.php';
                      </script>";
            } else {
                // Redirect to confirmation.php if voting is active
                echo "<script>
                        alert('Welcome Voter!');
                        window.location.href = 'dashboard/index.php';
                      </script>";
            }
        } else {
            $_SESSION['status'] = htmlspecialchars("Invalid ID Number or Password.", ENT_QUOTES, 'UTF-8');
            header("Location: index.php");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['status'] = htmlspecialchars("An error occurred: " . $e->getMessage(), ENT_QUOTES, 'UTF-8');
        header("Location: index.php");
        exit();
    }
}
?>