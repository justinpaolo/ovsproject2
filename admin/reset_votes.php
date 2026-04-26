
<?php
include('security.php');
include('include/header.php');
include('include/navbar.php');
include('include/conn.php');

// Handle reset action
if (isset($_POST['reset_voting'])) {
    try {
        // Delete all votes first so candidate/voter deletes do not violate foreign keys
        $conn->exec("DELETE FROM votes");
        // Remove all candidates
        $conn->exec("DELETE FROM candidate");
        // Remove all voter accounts except admin and other non-voter accounts
        $conn->exec("DELETE FROM voters WHERE account = 'Voter'");
        $message = "Voting has been reset. All candidates and voter records have been removed.";
        $messageClass = 'success';
    } catch (PDOException $e) {
        $message = "Error resetting election data: " . $e->getMessage();
        $messageClass = 'danger';
    }
}
?>

<div class="container mt-5">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h4 class="m-0 font-weight-bold text-danger">Reset Voting</h4>
        </div>
        <div class="card-body text-center">
            <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo htmlspecialchars($messageClass ?? 'success'); ?>"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            <form method="POST">
                <button type="submit" name="reset_voting" class="btn btn-danger"
                    onclick="return confirm('Are you sure you want to reset the election data? This will remove all votes, candidates, and voter entries. This cannot be undone!');">
                    <i class="fas fa-redo"></i> Reset Election Data
                </button>
            </form>
            <p class="mt-3 text-muted">This will delete all votes, candidate records, and voter records for the current election.</p>
        </div>
    </div>
</div>

<?php
include('include/scripts.php');
include('include/footer.php');
?>