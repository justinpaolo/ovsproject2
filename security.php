<?php
session_start();


// // Check if the user is logged in
// if (!isset($_SESSION['voter_id'])) {
//     header("Location: index.php");
//     exit();
// }

// // Check if the user is an admin (optional)
// if (isset($_SESSION['role']) && $_SESSION['role'] != 'voter') {
//     header("Location: ../index.php");
//     exit();
// }

// Optional: Prevent access if the voter has already voted
// if (isset($_SESSION['status']) && $_SESSION['status'] == "You have already voted. You cannot log in again.") {
//     header("Location: index.php");
//     exit();
// }
?>