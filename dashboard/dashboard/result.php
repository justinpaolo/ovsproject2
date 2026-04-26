<?php
session_start();
include("../admin/include/conn.php");

$voting_status = 1;
$showResults = false;
$nationalResults = [];
$localResults = [];

try {
    $settingsStmt = $conn->query("SELECT voting_status FROM settings WHERE id = 1");
    $settings = $settingsStmt->fetch(PDO::FETCH_ASSOC);
    if ($settings && isset($settings['voting_status'])) {
        $voting_status = (int) $settings['voting_status'];
    }

    if ($voting_status !== 1) {
        $showResults = true;

        // National results (election_type = 'National')
        $q1 = "SELECT candidate.img, candidate.firstname, candidate.lastname, candidate.position, candidate.grade_level, candidate.party, COUNT(votes.candidate_id) as vote_count 
                  FROM votes 
                  JOIN candidate ON votes.candidate_id = candidate.candidate_id 
                  WHERE candidate.election_type = 'National' 
                  GROUP BY votes.candidate_id, candidate.position 
                  ORDER BY candidate.position, vote_count DESC";
        $stmt = $conn->prepare($q1);
        $stmt->execute();
        $nationalResults = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Local results (election_type = 'Local')
        $q2 = "SELECT candidate.img, candidate.firstname, candidate.lastname, candidate.position, candidate.grade_level, candidate.party, candidate.department, COUNT(votes.candidate_id) as vote_count 
                  FROM votes 
                  JOIN candidate ON votes.candidate_id = candidate.candidate_id 
                  WHERE candidate.election_type = 'Local' 
                  GROUP BY votes.candidate_id, candidate.department, candidate.position 
                  ORDER BY candidate.department, candidate.position, vote_count DESC";
        $stmt2 = $conn->prepare($q2);
        $stmt2->execute();
        $localResults = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Failed to fetch results: " . $e->getMessage();
    header('Location: confirmation.php');
    exit();
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Results - Voting System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #e2ecec, #c8d5b9, #a4c6a4);
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .navbar {
            background: #e6f0ea;
            border-bottom: 1.5px solid #c3d8cc;
            box-shadow: 0 4px 12px rgba(115, 140, 115, 0.15);
            padding: 10px 25px;
            font-weight: 600;
            font-size: 18px;
            text-transform: capitalize;
        }
        .navbar .navbar-brand img {
            max-width: 150px;
            height: auto;
            margin-top: -10px;
        }
        .navbar .nav-link {
            color: #4a6a4a !important;
            padding: 20px 15px !important;
            transition: color 0.3s ease;
        }
        .navbar .nav-link:hover, .navbar .nav-link:focus {
            color: #7ca27c !important;
            text-decoration: underline;
        }
        .container1 {
            background: #e6f0ea;
            border: 1.5px solid #c3d8cc;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(40, 77, 40, 0.15);
            max-width: 900px;
            margin: 40px auto 60px;
            padding: 30px 40px;
            transition: all 0.3s ease;
            text-align: center;
        }
        .container1:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 28px rgba(115, 140, 115, 0.25);
        }
        .container1 h1 {
            color: #4a6a4a;
            font-weight: 700;
            margin-bottom: 30px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
            border-collapse: collapse;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
        }
        .table th,
        .table td {
            padding: 0.75rem;
            vertical-align: middle !important;
            border: 1px solid #c3d8cc;
        }
        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #c3d8cc;
            background-color: #6b8e6b;
            color: white;
        }
        .table tbody tr:nth-of-type(odd) {
            background-color: #f8f9fa;
        }
        .table tbody tr:hover {
            background-color: #e9ecef;
        }
        .candidate-img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 1.5px solid #c3d8cc;
        }
        .winner-row {
            background-color: #fff3cd !important;
            font-weight: 600;
            border: 2px solid #ffc107 !important;
        }
        .winner-row:hover {
            background-color: #ffe69c !important;
        }
        @media (max-width: 768px) {
            .container1 {
                padding: 15px 5px;
            }
            .navbar .nav-link {
                padding: 10px 5px !important;
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="image/16.png" alt="Logo">
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="view_candidates.php">View Candidates</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="result.php">Result</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container1">
        <h1>Voting Results</h1>
        <?php
        if (isset($_SESSION['error'])) {
            echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
            unset($_SESSION['error']);
        }
        if (!$showResults) {
            echo '<div class="alert alert-warning">Election is still ongoing. Results will be shown once the election is closed.</div>';
        } else {
            // National Results
            echo '<h2 style="margin-top:20px;">National Results</h2>';
            if (isset($nationalResults) && count($nationalResults) > 0) {
                // Find max votes for each position
                $maxVotesByPosition = [];
                foreach ($nationalResults as $r) {
                    $pos = $r['position'];
                    if (!isset($maxVotesByPosition[$pos]) || $r['vote_count'] > $maxVotesByPosition[$pos]) {
                        $maxVotesByPosition[$pos] = $r['vote_count'];
                    }
                }
                
                echo '<div class="table-responsive">';
                echo '<table class="table table-bordered table-striped">';
                echo '<thead>';
                echo '<tr>';
                echo '<th>Image</th>';
                echo '<th>First Name</th>';
                echo '<th>Last Name</th>';
                echo '<th>Position</th>';
                echo '<th>Year Level</th>';
                echo '<th>Party</th>';
                echo '<th>Votes</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';
                foreach ($nationalResults as $result) {
                    $isWinner = isset($maxVotesByPosition[$result['position']]) && $result['vote_count'] == $maxVotesByPosition[$result['position']];
                    $rowClass = $isWinner ? 'class="winner-row"' : '';
                    echo '<tr ' . $rowClass . '>';
                    if (!empty($result['img'])) {
                        echo '<td><img src="../admin/uploads/' . htmlspecialchars($result['img']) . '" class="candidate-img" alt="Candidate Image"></td>';
                    } else {
                        echo '<td><img src="../admin/uploads/default-profile.png" class="candidate-img" alt="No Photo"></td>';
                    }
                    echo '<td>' . htmlspecialchars($result['firstname']) . '</td>';
                    echo '<td>' . htmlspecialchars($result['lastname']) . '</td>';
                    echo '<td>' . htmlspecialchars($result['position']) . '</td>';
                    echo '<td>' . htmlspecialchars($result['grade_level']) . '</td>';
                    echo '<td>' . htmlspecialchars($result['party']) . '</td>';
                    echo '<td>' . htmlspecialchars($result['vote_count']) . '</td>';
                    echo '</tr>';
                }
                echo '</tbody>';
                echo '</table>';
                echo '</div>';
            } else {
                echo '<p>No national votes have been cast yet.</p>';
            }

            // Local Results grouped by department
            echo '<h2 style="margin-top:30px;">Local Results</h2>';
            if (isset($localResults) && count($localResults) > 0) {
                // Group by department
                $byDept = [];
                foreach ($localResults as $r) {
                    $dept = !empty($r['department']) ? $r['department'] : 'Unspecified';
                    if (!isset($byDept[$dept])) $byDept[$dept] = [];
                    $byDept[$dept][] = $r;
                }

                foreach ($byDept as $dept => $rows) {
                    echo '<h3 style="margin-top:20px;">Department: ' . htmlspecialchars($dept) . '</h3>';
                    
                    // Find max votes for each position in this department
                    $maxVotesLocal = [];
                    foreach ($rows as $r) {
                        $pos = $r['position'];
                        if (!isset($maxVotesLocal[$pos]) || $r['vote_count'] > $maxVotesLocal[$pos]) {
                            $maxVotesLocal[$pos] = $r['vote_count'];
                        }
                    }
                    
                    echo '<div class="table-responsive">';
                    echo '<table class="table table-bordered table-striped">';
                    echo '<thead>';
                    echo '<tr>';
                    echo '<th>Image</th>';
                    echo '<th>First Name</th>';
                    echo '<th>Last Name</th>';
                    echo '<th>Position</th>';
                    echo '<th>Year Level</th>';
                    echo '<th>Party</th>';
                    echo '<th>Votes</th>';
                    echo '</tr>';
                    echo '</thead>';
                    echo '<tbody>';
                    foreach ($rows as $result) {
                        $isWinner = isset($maxVotesLocal[$result['position']]) && $result['vote_count'] == $maxVotesLocal[$result['position']];
                        $rowClass = $isWinner ? 'class="winner-row"' : '';
                        echo '<tr ' . $rowClass . '>';
                        if (!empty($result['img'])) {
                            echo '<td><img src="../admin/uploads/' . htmlspecialchars($result['img']) . '" class="candidate-img" alt="Candidate Image"></td>';
                        } else {
                            echo '<td><img src="../admin/uploads/default-profile.png" class="candidate-img" alt="No Photo"></td>';
                        }
                        echo '<td>' . htmlspecialchars($result['firstname']) . '</td>';
                        echo '<td>' . htmlspecialchars($result['lastname']) . '</td>';
                        echo '<td>' . htmlspecialchars($result['position']) . '</td>';
                        echo '<td>' . htmlspecialchars($result['grade_level']) . '</td>';
                        echo '<td>' . htmlspecialchars($result['party']) . '</td>';
                        echo '<td>' . htmlspecialchars($result['vote_count']) . '</td>';
                        echo '</tr>';
                    }
                    echo '</tbody>';
                    echo '</table>';
                    echo '</div>';
                }
            } else {
                echo '<p>No local votes have been cast yet.</p>';
            }
        }
        ?>
    </div>

    <script src="js/jquery-3.2.1.slim.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>
</html>

