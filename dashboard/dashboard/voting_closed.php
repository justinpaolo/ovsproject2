<?php
session_start();
include("../admin/include/conn.php"); // Include database connection

// Get current page name
$current_page = basename($_SERVER['PHP_SELF']); 

try {
    $query = "SELECT voting_status FROM settings WHERE id = 1";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $voting_status = $stmt->fetchColumn();

    if ($voting_status != 1 && $current_page != "voting_closed.php") {
        // Redirect only if not already on voting_closed.php
        header("Location: voting_closed.php");
        exit();
    }
} catch (PDOException $e) {
    echo "<script>
            alert('An error occurred: " . $e->getMessage() . "');
            window.location.href = '../index.php';
          </script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voting Closed</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .message-box {
            text-align: center;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .message-box h1 {
            font-size: 24px;
            color: #dc3545;
        }
        .message-box p {
            font-size: 18px;
            color: #555;
        }
        .btn {
            margin-top: 20px;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Periodically check the voting status
        setInterval(function() {
            $.ajax({
                url: "check_voting_status.php", // Endpoint to check voting status
                method: "GET",
                success: function(response) {
                    console.log("Voting Status Response:", response); // Debugging
                    if (response.trim() === "1") {
                        // Redirect to confirmation.php if voting is enabled
                        window.location.href = "confirmation.php";
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", error); // Debugging
                }
            });
        }, 3000); // Check every 3 seconds
    </script>
</head>
<body>
    <div class="message-box">
        <h1>Voting is Closed</h1>
        <p>We are sorry, but voting has not started yet, please wait.</p>
        <a href="../index.php" class="btn btn-primary">Click to go back to login</a>
    </div>
</body>
</html>