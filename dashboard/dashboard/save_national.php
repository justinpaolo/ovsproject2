<?php
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

session_start();
include('../admin/include/conn.php');

// Check voting status
try {
    $query = "SELECT voting_status FROM settings WHERE id = 1";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $voting_status = $stmt->fetchColumn();

    if ($voting_status != 1) {
        header("Location: voting_closed.php");
        exit();
    }
} catch (PDOException $e) {
    header("Location: voting_closed.php");
    exit();
}

// Ensure voter logged in
if (!isset($_SESSION['voter_id'])) {
    header('Location: ../index.php');
    exit();
}

$voter_id = $_SESSION['voter_id'];

// Prevent double voting
try {
    $checkIfVoted = "SELECT COUNT(*) FROM votes WHERE voter_id = ?";
    $stmt = $conn->prepare($checkIfVoted);
    $stmt->execute([$voter_id]);
    $voteCount = $stmt->fetchColumn();
    if ($voteCount > 0) {
        echo "<script>alert('You have already submitted your vote.'); window.location.href='voter_result.php';</script>";
        exit();
    }
} catch (PDOException $e) {
    echo "<script>alert('Error checking vote status.'); window.location.href='../index.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Save all posted national selections into session so we can insert them after local voting
    // Filter out any unexpected fields (we accept all posted name=>candidate_id pairs)
    $national_votes = [];
    foreach ($_POST as $k => $v) {
        // ignore empty values
        if ($v === '' || $v === null) continue;
        // store numeric candidate ids only
        if (is_numeric($v)) {
            $national_votes[$k] = (int)$v;
        }
    }

    if (!empty($national_votes)) {
        $_SESSION['saved_votes'] = $_SESSION['saved_votes'] ?? [];
        $_SESSION['saved_votes']['national'] = $national_votes;
    }

    // Redirect user to local election to continue voting
    header('Location: local_election.php');
    exit();
} else {
    // No POST — redirect to national election page
    header('Location: national_election.php');
    exit();
}

?>
