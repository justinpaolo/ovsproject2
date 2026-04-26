<?php

include('include/conn.php');

if (isset($_POST['approve_btn'])) {
    $candidate_id = $_POST['candidate_id'];

    try {
        $query = "UPDATE candidate SET status = 'approved' WHERE candidate_id = :candidate_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':candidate_id', $candidate_id);
        $stmt->execute();

        $_SESSION['success'] = "Candidate approved successfully.";
        header('Location: candidates_approval.php');
        exit();
    } catch (PDOException $e) {
        $_SESSION['status'] = "Error: " . $e->getMessage();
        header('Location: candidates_approval.php');
        exit();
    }
}

if (isset($_POST['reject_btn'])) {
    $candidate_id = $_POST['candidate_id'];

    try {
        $query = "UPDATE candidate SET status = 'rejected' WHERE candidate_id = :candidate_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':candidate_id', $candidate_id);
        $stmt->execute();

        $_SESSION['success'] = "Candidate rejected successfully.";
        header('Location: candidates_approval.php');
        exit();
    } catch (PDOException $e) {
        $_SESSION['status'] = "Error: " . $e->getMessage();
        header('Location: candidates_approval.php');
        exit();
    }
}
?>