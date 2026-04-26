<?php
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

session_start();
include("../admin/include/conn.php");

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

// Check if voter_id is set in the session
if (!isset($_SESSION['voter_id'])) {
    header("Location: ../index.php");
    exit();
}

$voter_id = $_SESSION['voter_id'];

echo "<script>console.log('Voter ID: " . $voter_id . "');</script>";

// Check if the voter has already voted
try {
    $checkIfVoted = "SELECT COUNT(*) FROM votes WHERE voter_id = ?";
    $stmt = $conn->prepare($checkIfVoted);
    $stmt->execute([$voter_id]);
    $voteCount = $stmt->fetchColumn();

    if ($voteCount > 0) {
        // If the voter has already voted, redirect to voter_result.php
        echo "<script>
                alert('You have already submitted your vote.');
                window.location.href = 'voter_result.php';
              </script>";
        exit();
    }
} catch (PDOException $e) {
    echo "<script>
            alert('An error occurred while checking your vote status: " . $e->getMessage() . "');
            window.location.href = '../index.php';
          </script>";
    exit();
}

// Proceed with voting logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // First insert any saved national votes stored in session by save_national.php
        if (isset($_SESSION['saved_votes']['national']) && is_array($_SESSION['saved_votes']['national'])) {
            foreach ($_SESSION['saved_votes']['national'] as $pos => $candidate_id) {
                // ensure numeric and skip if empty
                if (!$candidate_id || !is_numeric($candidate_id)) continue;
                $query = "INSERT INTO votes (candidate_id, voter_id, vote_time) VALUES (?, ?, NOW())";
                $stmt = $conn->prepare($query);
                $stmt->execute([(int)$candidate_id, $voter_id]);
            }
            // clear national saved votes after inserting
            unset($_SESSION['saved_votes']['national']);
        }

        // Then handle posted local votes (from local_election.php)
        // We accept any posted position=>candidate_id pairs for local positions
        foreach ($_POST as $field => $candidate_id) {
            // skip non-numeric or special control fields like current_department
            if ($field === 'current_department') continue;
            if (!is_numeric($candidate_id)) continue;
            $query = "INSERT INTO votes (candidate_id, voter_id, vote_time) VALUES (?, ?, NOW())";
            $stmt = $conn->prepare($query);
            $stmt->execute([(int)$candidate_id, $voter_id]);
        }

        // Get department list and current department
        $deptQuery = "SELECT DISTINCT department FROM candidate WHERE election_type = 'Local' AND status = 'approved' ORDER BY department";
        $deptStmt = $conn->query($deptQuery);
        $departments = $deptStmt->fetchAll(PDO::FETCH_COLUMN);
        $currentDept = isset($_POST['current_department']) ? $_POST['current_department'] : '';

        // Find next department
        $nextDept = '';
        if ($currentDept && in_array($currentDept, $departments)) {
            $currentIndex = array_search($currentDept, $departments);
            if ($currentIndex !== false && $currentIndex < count($departments) - 1) {
                $nextDept = $departments[$currentIndex + 1];
            }
        }

        // If next department exists, redirect to local_election.php for next department
        if ($nextDept) {
            echo "<script>
                alert('Your vote for $currentDept has been submitted! Proceeding to next department.');
                window.location.href = 'local_election.php?department=" . urlencode($nextDept) . "';
            </script>";
            exit();
        } else {
            // Last department, update voter status and redirect to results
            try {
                $updateStatus = "UPDATE voters SET status = 1 WHERE voter_id = ?";
                $stmt = $conn->prepare($updateStatus);
                $stmt->execute([$voter_id]);
            } catch (PDOException $e) {
                echo "<script>
                        alert('An error occurred while updating your voting status: " . $e->getMessage() . "');
                      </script>";
                exit();
            }
            echo "<script>
                alert('Your vote has been successfully submitted for all departments!');
                window.location.href = 'voter_result.php';
            </script>";
            exit();
        }
    } catch (PDOException $e) {
        echo "<script>
                alert('An error occurred while submitting your vote: " . $e->getMessage() . "');
                window.location.href = 'confirmation.php';
              </script>";
        exit();
    }
}
?>