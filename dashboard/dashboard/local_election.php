<?php
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

session_start();
require_once '../admin/include/conn.php';

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

// Require login
if (!isset($_SESSION['voter_id'])) {
    header('Location: ../index.php');
    exit();
}

// Prevent access to Local election until the voter has a National vote recorded.
// This ensures voters go through National -> Local flow.
try {
    $voter_id_check = $_SESSION['voter_id'];
    $natCheck = $conn->prepare("SELECT COUNT(*) FROM votes v JOIN candidate c ON v.candidate_id = c.candidate_id WHERE v.voter_id = ? AND c.election_type = 'National'");
    $natCheck->execute([$voter_id_check]);
    $natCount = (int) $natCheck->fetchColumn();

    // Also allow if the user has saved national selections in session (not yet submitted)
    $sessionNatCount = 0;
    if (isset($_SESSION['saved_votes']) && is_array($_SESSION['saved_votes'])) {
        if (isset($_SESSION['saved_votes']['national']) && is_array($_SESSION['saved_votes']['national'])) {
            $sessionNatCount = count($_SESSION['saved_votes']['national']);
        }
    }

    if ($natCount === 0 && $sessionNatCount === 0) {
        $_SESSION['status'] = 'Please vote in the National election before voting in the Local election.';
        header('Location: index.php');
        exit();
    }
} catch (Exception $e) {
    // On DB error, allow page to load (fail-open) but set a session message for debugging.
    // If session contains saved national votes, allow access.
    $sessionNatCount = 0;
    if (isset($_SESSION['saved_votes']) && isset($_SESSION['saved_votes']['national']) && is_array($_SESSION['saved_votes']['national'])) {
        $sessionNatCount = count($_SESSION['saved_votes']['national']);
    }
    if ($sessionNatCount === 0) {
        $_SESSION['status'] = 'Unable to verify voting order. Please complete National voting first.';
    }
}

// Get unique departments for dropdown
$deptQuery = "SELECT DISTINCT department FROM candidate WHERE election_type = 'Local' AND status = 'approved' ORDER BY department";
$deptStmt = $conn->query($deptQuery);
$departments = $deptStmt->fetchAll(PDO::FETCH_COLUMN);

// Get selected department from GET or session (user department)
$selectedDept = '';
if (isset($_GET['department']) && $_GET['department'] !== '') {
    $selectedDept = $_GET['department'];
} elseif (isset($_SESSION['department']) && $_SESSION['department'] !== '') {
    $selectedDept = $_SESSION['department'];
}

// Fetch candidates filtered by department if selected
$candQuery = "SELECT * FROM candidate WHERE election_type = 'Local' AND status = 'approved'";
if ($selectedDept) {
    $candQuery .= " AND department = :department";
}
$candQuery .= " ORDER BY position, firstname";
$candStmt = $conn->prepare($candQuery);
if ($selectedDept) {
    $candStmt->bindParam(':department', $selectedDept);
    $candStmt->execute();
} else {
    $candStmt->execute();
}
$candidates = $candStmt->fetchAll();

// Group candidates by position
$grouped = [];
foreach ($candidates as $row) {
    $grouped[$row['position']][] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vote for Your Local Candidates</title>
    <link rel="stylesheet" href="dashboard/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #e2ecec, #c8d5b9, #a4c6a4);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .vote-container {
            background: #e6f0ea;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(115,140,115,0.15);
            max-width: 700px;
            margin: 40px auto;
            padding: 32px 24px;
            text-align: center;
        }
        .vote-title {
            font-size: 1.7rem;
            font-weight: 700;
            color: #355c36;
            margin-bottom: 28px;
        }
        .section {
            background: #d5e8d4;
            border-radius: 12px;
            margin-bottom: 24px;
            padding: 18px 0 8px 0;
        }
        .section-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #355c36;
            margin-bottom: 18px;
        }
        .candidate-row {
            display: flex;
            justify-content: center;
            gap: 32px;
            margin-bottom: 18px;
        }
        .candidate-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(107,142,107,0.15);
            padding: 24px 18px 18px 18px;
            width: 220px;
            text-align: center;
            cursor: pointer;
            transition: box-shadow 0.2s, border 0.2s;
            border: 2px solid transparent;
        }
        .candidate-card.selected {
            box-shadow: 0 0 0 4px #8fbf8f, 0 8px 20px rgba(115,140,115,0.15);
            border: 2px solid #8fbf8f;
        }
        .candidate-card:hover {
            box-shadow: 0 0 0 2px #a3cca3, 0 8px 20px rgba(115,140,115,0.18);
        }
        .candidate-card img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 12px;
            border: 2px solid #c3d8cc;
        }
        .candidate-name {
            font-weight: 700;
            color: #355c36;
            margin-bottom: 4px;
        }
        .candidate-details {
            font-size: 0.95rem;
            color: #6b8e6b;
            margin-bottom: 6px;
        }
        .candidate-party {
            font-size: 0.92rem;
            color: #7ca27c;
            margin-bottom: 10px;
        }
        .select-radio {
            margin-bottom: 0;
        }
        .submit-btn {
            background: linear-gradient(45deg, #8fbf8f, #6b8e6b);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 10px 32px;
            font-weight: 600;
            font-size: 1rem;
            margin-top: 18px;
            box-shadow: 0 4px 12px rgba(107,142,107,0.15);
            cursor: pointer;
            transition: background 0.3s;
            opacity: 0.7;
        }
        .submit-btn.selected, .submit-btn:enabled {
            opacity: 1;
            box-shadow: 0 0 0 4px #8fbf8f, 0 8px 20px rgba(115,140,115,0.15);
        }
        .submit-btn:disabled {
            cursor: not-allowed;
        }
    </style>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Build an array of position names to track selections
        const sectionEls = document.querySelectorAll('.section');
        const positionNames = Array.from(sectionEls).map(section => section.querySelector('.section-title').textContent.trim().toLowerCase().replace(/[\s\-]/g, '_'));
        const submitBtn = document.getElementById('submit-btn');
        const form = document.getElementById('voteForm');
        function updateSelection() {
            let allSelected = true;
            positionNames.forEach(position => {
                if (!form.querySelector('input[name="'+position+'"]:checked')) {
                    allSelected = false;
                }
            });
            if (allSelected) {
                submitBtn.removeAttribute('disabled');
                submitBtn.classList.add('selected');
            } else {
                submitBtn.setAttribute('disabled', 'disabled');
                submitBtn.classList.remove('selected');
            }
        }
        // Highlight selected card
        document.querySelectorAll('.candidate-card input[type="radio"]').forEach(function(radio) {
            radio.addEventListener('change', function() {
                radio.closest('.candidate-row').querySelectorAll('.candidate-card').forEach(function(card) {
                    card.classList.remove('selected');
                });
                if (radio.checked) {
                    radio.closest('.candidate-card').classList.add('selected');
                }
                updateSelection();
            });
        });
        // Show confirm dialog summary before submitting
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            let summary = 'You voted for:\n';
            positionNames.forEach(position => {
                const checked = form.querySelector('input[name="'+position+'"]:checked');
                if (checked) {
                    const name = checked.closest('.candidate-card').querySelector('.candidate-name').textContent;
                    summary += position.replace(/_/g,' ').toUpperCase() + ': ' + name + '\n';
                }
            });
            if (confirm(summary + "\n\nPress OK to confirm, or Cancel to review your choices.")) {
                form.submit();
            }
        });
        updateSelection();
    });
    </script>
</head>
<body>
    <div class="vote-container">
        <div class="vote-title">Vote for Your Local Candidates</div>
        <!-- Show the user's department (non-editable) -->
        <div style="margin-bottom: 24px; text-align:center;">
            <label style="font-weight:600; color:#355c36; display:block; margin-bottom:8px;">Your Department</label>
            <div style="display:inline-block; background:#ffffff; padding:8px 14px; border-radius:8px; color:#355c36; font-weight:600;">
                <?php echo htmlspecialchars($selectedDept ?: ($_SESSION['department'] ?? '')); ?>
            </div>
        </div>
        <form action="submit_vote.php" method="POST" id="voteForm">
            <!-- include department as hidden field so submit_vote.php receives it (user cannot change it here) -->
            <input type="hidden" name="department" value="<?php echo htmlspecialchars($selectedDept ?: ($_SESSION['department'] ?? '')); ?>">
            <?php if (!empty($grouped)): ?>
                <?php foreach ($grouped as $position => $cands): ?>
                    <div class="section">
                        <div class="section-title"><?php echo htmlspecialchars($position); ?></div>
                        <div class="candidate-row">
                            <?php foreach ($cands as $cand): ?>
                                <label class="candidate-card" style="cursor:pointer;">
                                    <?php if (!empty($cand['img'])): ?>
                                        <img src="../admin/uploads/<?php echo htmlspecialchars($cand['img']); ?>" alt="Candidate Image">
                                    <?php endif; ?>
                                    <div class="candidate-name"><?php echo htmlspecialchars($cand['firstname'] . ' ' . $cand['lastname']); ?></div>
                                    <div class="candidate-details">DEPARTMENT: <?php echo htmlspecialchars($cand['department']); ?></div>
                                    <div class="candidate-party">PARTY: <?php echo htmlspecialchars($cand['party']); ?></div>
                     <input type="radio"
                         name="<?php echo strtolower(preg_replace('/[\s\-]+/', '_', $position)); ?>"
                                           value="<?php echo $cand['candidate_id']; ?>"
                                           class="select-radio"
                                           style="display:none;">
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert alert-info">No candidates found for this department.</div>
            <?php endif; ?>
            <button type="submit" class="submit-btn" id="submit-btn" disabled>Submit Vote</button>
        </form>
    </div>
</body>
</html>
