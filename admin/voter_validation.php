<?php
ob_start();
session_start();
include('include/conn.php');
include('security.php');
include('include/header.php');
include('include/navbar.php');

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: adminlogin.php");
    exit();
}

// Handle accept/reject actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $voter_id = $_POST['voter_id'];
    $action = $_POST['action']; // 'accept' or 'reject'

    try {
        if ($action === 'accept') {
            $query = "UPDATE voters SET validation_status = 'accepted' WHERE voter_id = ?";
        } elseif ($action === 'reject') {
            $query = "UPDATE voters SET validation_status = 'rejected' WHERE voter_id = ?";
        }

        $stmt = $conn->prepare($query);
        $stmt->execute([$voter_id]);

        $_SESSION['success'] = "Voter has been " . ($action === 'accept' ? 'accepted' : 'rejected') . " successfully.";
        header("Location: voter_validation.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "An error occurred: " . $e->getMessage();
        header("Location: voter_validation.php");
        exit();
    }
}

// Fetch pending voters
try {
    $query = "SELECT * FROM voters WHERE validation_status = 'pending'";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $pending_voters = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voter Validation</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Voter Validation</h2>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <?php if (count($pending_voters) > 0): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Voter ID</th>
                        <th>Full Name</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pending_voters as $voter): ?>
                        <tr>
                            <td><?php echo $voter['voter_id']; ?></td>
                            <td><?php echo ucwords($voter['firstname'] . ' ' . $voter['lastname']); ?></td>
                            <td><?php echo $voter['date']; ?></td>
                            <td>
                                <form method="POST" style="display: inline-block;">
                                    <input type="hidden" name="voter_id" value="<?php echo $voter['voter_id']; ?>">
                                    <button type="submit" name="action" value="accept" class="btn btn-success btn-sm">Accept</button>
                                </form>
                                <form method="POST" style="display: inline-block;">
                                    <input type="hidden" name="voter_id" value="<?php echo $voter['voter_id']; ?>">
                                    <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm">Reject</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-center">No pending voters found.</p>
        <?php endif; ?>
    </div>
</body>
</html>

<?php

include('include/scripts.php');


?>