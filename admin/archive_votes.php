<?php
session_start();

include('security.php');
include('include/header.php');
include('include/navbar.php');
include('include/conn.php');

$message = '';
$error = '';

// get current counts
try {
    $totalStmt = $conn->query("SELECT COUNT(*) FROM votes");
    $totalVotes = (int)$totalStmt->fetchColumn();

    $natStmt = $conn->prepare("SELECT COUNT(*) FROM votes v JOIN candidate c ON v.candidate_id = c.candidate_id WHERE c.election_type = 'National'");
    $natStmt->execute();
    $natVotes = (int)$natStmt->fetchColumn();

    $locStmt = $conn->prepare("SELECT COUNT(*) FROM votes v JOIN candidate c ON v.candidate_id = c.candidate_id WHERE c.election_type = 'Local'");
    $locStmt->execute();
    $locVotes = (int)$locStmt->fetchColumn();
} catch (Exception $e) {
    $error = 'Could not read vote counts: ' . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
    $type = isset($_POST['type']) ? $_POST['type'] : 'all';

    try {
        $conn->beginTransaction();

        if ($type === 'all') {
            $ins = $conn->prepare("INSERT INTO votes_archive (vote_id, candidate_id, voter_id, vote_time) SELECT vote_id, candidate_id, voter_id, vote_time FROM votes");
            $ins->execute();
            $moved = $ins->rowCount();

            $del = $conn->prepare("DELETE FROM votes");
            $del->execute();
        } else {
            // expecting 'National' or 'Local'
            $ins = $conn->prepare("INSERT INTO votes_archive (vote_id, candidate_id, voter_id, vote_time) SELECT v.vote_id, v.candidate_id, v.voter_id, v.vote_time FROM votes v JOIN candidate c ON v.candidate_id = c.candidate_id WHERE c.election_type = ?");
            $ins->execute([$type]);
            $moved = $ins->rowCount();

            $del = $conn->prepare("DELETE v FROM votes v JOIN candidate c ON v.candidate_id = c.candidate_id WHERE c.election_type = ?");
            $del->execute([$type]);
        }

        $conn->commit();
        $message = "Archived {$moved} votes (type: {$type}).";
        // refresh counts
        $totalStmt = $conn->query("SELECT COUNT(*) FROM votes");
        $totalVotes = (int)$totalStmt->fetchColumn();
        $natStmt->execute(); $natVotes = (int)$natStmt->fetchColumn();
        $locStmt->execute(); $locVotes = (int)$locStmt->fetchColumn();
    } catch (Exception $e) {
        $conn->rollBack();
        $error = 'Archive failed: ' . $e->getMessage();
    }
}

?>
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Archive Votes</h6>
        </div>
        <div class="card-body">
            <?php if (!empty($message)): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <p>Current votes:</p>
            <ul>
                <li>Total votes: <?php echo $totalVotes; ?></li>
                <li>National votes: <?php echo $natVotes; ?></li>
                <li>Local votes: <?php echo $locVotes; ?></li>
            </ul>

            <form method="post" onsubmit="return confirm('Are you sure you want to archive the selected votes? This will move them to votes_archive and remove them from the active votes table.');">
                <div class="form-group">
                    <label>Archive type</label>
                    <select name="type" class="form-control">
                        <option value="all">All votes (national + local)</option>
                        <option value="National">National only</option>
                        <option value="Local">Local only</option>
                    </select>
                </div>
                <button type="submit" name="confirm" value="1" class="btn btn-primary">Archive</button>
            </form>

            <hr>
            <p>Notes:</p>
            <ul>
                <li>This moves rows from <code>votes</code> to <code>votes_archive</code> and deletes them from <code>votes</code>. Make a DB backup before running.</li>
                <li>Only admins should use this. The page is protected by <code>security.php</code>.</li>
            </ul>
        </div>
    </div>
</div>

<?php
// include('include/scripts.php');
// include('include/footer.php');
?>
