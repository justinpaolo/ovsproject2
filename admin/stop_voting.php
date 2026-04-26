<?php
session_start();
include('include/conn.php');

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    echo "<script>
            alert('You are not logged in as admin. Redirecting to login page.');
            window.location.href = 'adminlogin.php';
          </script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Disable voting
        $query = "UPDATE settings SET voting_status = 0 WHERE id = 1";
        $stmt = $conn->prepare($query);
        if ($stmt->execute()) {
            echo "<script>
                    alert('Voting has been stopped successfully!');
                    window.location.href = 'index.php';
                  </script>";
        } else {
            echo "<script>
                    alert('Failed to stop voting. Please try again.');
                    window.location.href = 'stop_voting.php';
                  </script>";
        }
        exit();
    } catch (PDOException $e) {
        echo "<script>
                alert('An error occurred: " . $e->getMessage() . "');
                window.location.href = 'stop_voting.php';
              </script>";
        exit();
    }
}

// Check the current voting status
$query = "SELECT voting_status FROM settings WHERE id = 1";
$stmt = $conn->prepare($query);
$stmt->execute();
$status = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stop Voting</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Stop Voting</h2>
        <div class="alert alert-info text-center">
            Current Voting Status: <strong><?php echo $status == 1 ? 'Enabled' : 'Disabled'; ?></strong>
        </div>
        <form method="POST">
            <button type="submit" class="btn btn-danger btn-block">Stop Voting</button>
        </form>
    </div>
</body>
</html>