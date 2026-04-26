<?php
session_start();
include('include/conn.php');

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: adminlogin.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Enable voting
        $query = "UPDATE settings SET voting_status = 1 WHERE id = 1";
        $stmt = $conn->prepare($query);
        $stmt->execute();

        echo "<script>
                    alert('Voting has started!');
                    window.location.href = 'index.php';
                  </script>";
        exit();
    } catch (PDOException $e) {
        $_SESSION['status'] = "An error occurred: " . $e->getMessage();
        header("Location: start_voting.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Start Voting</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Start Voting</h2>
        <?php
        if (isset($_SESSION['success'])) {
            echo "<div class='alert alert-success'>" . $_SESSION['success'] . "</div>";
            unset($_SESSION['success']);
        }
        if (isset($_SESSION['status'])) {
            echo "<div class='alert alert-danger'>" . $_SESSION['status'] . "</div>";
            unset($_SESSION['status']);
        }
        ?>
        <form method="POST">
            <button type="submit" class="btn btn-success btn-block">Start Voting</button>
        </form>
    </div>
</body>
</html>