<?php
session_start();

include('../include/conn.php'); // Adjust the path as necessary

// Check if the user has a voter role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'voter') {
    $_SESSION['status'] = "Access Denied. Only voters can vote.";
    header('Location: index.php');
    exit();
}

// Process Vote
if (isset($_POST['vote_btn'])) {
    $candidate_id = $_POST['candidate_id'];
    $voter_id = $_SESSION['voter_id']; // Assuming you have a session variable for the voter

    try {
        // Insert the vote into the votes table
        $query = "INSERT INTO votes (voter_id, candidate_id) VALUES (:voter_id, :candidate_id)";
        $stmt = $conn->prepare($query);
        $stmt->execute([
            'voter_id' => $voter_id,
            'candidate_id' => $candidate_id
        ]);

        // Update the status of the voter to 'voted'
        $update_query = "UPDATE voters SET status = 1 WHERE voter_id = :voter_id";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->execute(['voter_id' => $voter_id]);

        if ($update_stmt->rowCount() > 0) {
            $_SESSION['success'] = "Vote cast successfully!";
        } else {
            $_SESSION['status'] = "Failed to update voter status.";
        }
        header('Location: vote_candidates.php');
        exit();
    } catch (PDOException $e) {
        $_SESSION['status'] = "Vote Error: " . $e->getMessage();
        header('Location: vote_candidates.php');
        exit();
    }
}
?>