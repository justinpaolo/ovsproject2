<?php
include('security.php');
include('include/header.php');
include('include/navbar.php');
include('include/conn.php');
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Voting Results</h1>

    <!-- Election type selection buttons -->
    <form method="GET" style="margin-bottom: 24px; display:flex; gap:10px; align-items:center;">
        <button type="submit" name="election_type" value="National" class="btn btn-primary" style="padding:8px 16px;">
            National Election
        </button>
        <button type="submit" name="election_type" value="Local" class="btn btn-success" style="padding:8px 16px;">
            Local Election
        </button>
        <?php if (isset($_GET['election_type']) && $_GET['election_type']): ?>
            <a href="result.php" class="btn btn-secondary" style="padding:8px 16px;">Show All</a>
        <?php endif; ?>
    </form>

    <?php 
    try {
        // Get selected election type
        $selectedType = isset($_GET['election_type']) ? $_GET['election_type'] : '';

        // Get total votes for selected election type
        $totalVotesQuery = "SELECT COUNT(*) as total_votes FROM votes ";
        if ($selectedType) {
            $totalVotesQuery .= "JOIN candidate ON votes.candidate_id = candidate.candidate_id WHERE candidate.election_type = :election_type";
        }
        $totalVotesStmt = $conn->prepare($totalVotesQuery);
        if ($selectedType) {
            $totalVotesStmt->bindParam(':election_type', $selectedType);
            $totalVotesStmt->execute();
        } else {
            $totalVotesStmt->execute();
        }
        $totalVotesRow = $totalVotesStmt->fetch();
        $totalVotes = $totalVotesRow['total_votes'];

        // Get all candidates and their votes for selected election type
        $query = "SELECT candidate.candidate_id, candidate.election_type, candidate.img, candidate.position, candidate.firstname, candidate.lastname, candidate.department, candidate.party, candidate.date_registered, COUNT(votes.candidate_id) as vote_count 
                  FROM candidate 
                  LEFT JOIN votes ON candidate.candidate_id = votes.candidate_id 
                  WHERE candidate.status = 'approved' ";
        if ($selectedType) {
            $query .= "AND candidate.election_type = :election_type ";
        }
        $query .= "GROUP BY candidate.candidate_id ORDER BY candidate.position, vote_count DESC";
        $stmt = $conn->prepare($query);
        if ($selectedType) {
            $stmt->bindParam(':election_type', $selectedType);
            $stmt->execute();
        } else {
            $stmt->execute();
        }

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $candidates = [];
        $votes = [];
        $percentages = [];
        $pieData = [];
        $winningVoteCount = [];

        foreach ($results as $row) {
            $key = trim($row['election_type'] . '|' . $row['position']);
            if (!isset($winningVoteCount[$key]) || $row['vote_count'] > $winningVoteCount[$key]) {
                $winningVoteCount[$key] = $row['vote_count'];
            }
        }

        if(count($results) > 0) {
            echo '<div class="table-responsive">';
            echo '<table class="table table-bordered table-striped">';
            echo '<thead class="thead-dark">';
            echo '<tr>';
            echo '<th>Election Type</th>';
            echo '<th>Position</th>';
            echo '<th>First Name</th>';
            echo '<th>Last Name</th>';
            echo '<th>Department</th>';
            echo '<th>Party</th>';
            echo '<th>Date Registered</th>';
            echo '<th>Votes</th>';
            echo '<th>Percentage</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            foreach ($results as $row) {
                $vote_count = $row['vote_count'];
                $percentage = ($totalVotes > 0) ? round(($vote_count / $totalVotes) * 100, 2) : 0;
                $key = trim($row['election_type'] . '|' . $row['position']);
                $isWinner = ($vote_count > 0 && $vote_count === $winningVoteCount[$key]);
                $rowClass = $isWinner ? ' style="background-color:#fff3cd;color:#856404;font-weight:600;"' : '';
                echo '<tr' . $rowClass . '>';
                echo '<td>' . htmlspecialchars($row['election_type']) . '</td>';
                echo '<td>' . htmlspecialchars($row['position']) . '</td>';
                echo '<td>' . htmlspecialchars($row['firstname']) . '</td>';
                echo '<td>' . htmlspecialchars($row['lastname']) . '</td>';
                echo '<td>' . htmlspecialchars($row['department']) . '</td>';
                echo '<td>' . htmlspecialchars($row['party']) . '</td>';
                echo '<td>' . htmlspecialchars($row['date_registered']) . '</td>';
                echo '<td>' . $vote_count . '</td>';
                echo '<td>' . $percentage . '%</td>';
                echo '</tr>';

                $candidates[] = $row['firstname'] . ' ' . $row['lastname'];
                $votes[] = $vote_count;
                $percentages[] = $percentage;

                $pos = $row['position'];
                if (!isset($pieData[$pos])) {
                    $pieData[$pos] = [
                        'labels' => [],
                        'votes' => [],
                    ];
                }
                $pieData[$pos]['labels'][] = $row['firstname'] . ' ' . $row['lastname'];
                $pieData[$pos]['votes'][] = $vote_count;
            }
            echo '</tbody>';
            echo '</table>';
            echo '</div>';
        } else {
            ?>
            <div class="alert alert-info text-center">
                No voting results found at this time.
            </div>
            <?php
        }
    } catch(PDOException $e) {
        ?>
        <div class="alert alert-danger">
            Error: <?php echo $e->getMessage(); ?>
        </div>
        <?php
    }
    ?>

    <?php
    // Count total voters who have already voted
    $totalVotersQuery = "SELECT COUNT(DISTINCT voter_id) AS total_voters FROM votes";
    $totalVotersStmt = $conn->query($totalVotersQuery);
    $totalVotersRow = $totalVotersStmt->fetch();
    $totalVoters = $totalVotersRow['total_voters'];
    ?>

    <div class="alert alert-success mb-4" style="font-size:1.2rem;">
        <strong>Total Voters Who Voted:</strong> <?php echo $totalVoters; ?>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Voting Results Line Chart</h6>
        </div>
        <div class="card-body">
            <div class="chart-area">
                <canvas id="myBarChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Pie charts for each position -->
    <?php foreach ($pieData as $position => $data): ?>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success"><?php echo htmlspecialchars($position); ?> Results</h6>
            </div>
            <div class="card-body">
                <canvas id="pieChart_<?php echo md5($position); ?>" width="300" height="300" style="max-width:300px;max-height:300px;"></canvas>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php
include('include/scripts.php');
?>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Line chart
    var ctx = document.getElementById('myBarChart').getContext('2d');
    var myBarChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($candidates); ?>,
            datasets: [{
                label: 'Votes',
                data: <?php echo json_encode($votes); ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2,
                fill: true
            },
            {
                label: 'Percentage',
                data: <?php echo json_encode($percentages); ?>,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 2,
                yAxisID: 'percentage',
                fill: true
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Votes'
                    }
                },
                percentage: {
                    position: 'right',
                    beginAtZero: true,
                    min: 0,
                    max: 100,
                    title: {
                        display: true,
                        text: 'Percentage (%)'
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    labels: {
                        color: 'rgb(54, 162, 235)'
                    }
                }
            }
        }
    });

    // Pie charts for each position
    <?php foreach ($pieData as $position => $data): ?>
    var ctxPie_<?php echo md5($position); ?> = document.getElementById('pieChart_<?php echo md5($position); ?>').getContext('2d');
    var pieChart_<?php echo md5($position); ?> = new Chart(ctxPie_<?php echo md5($position); ?>, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode($data['labels']); ?>,
            datasets: [{
                data: <?php echo json_encode($data['votes']); ?>,
                backgroundColor: [
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(153, 102, 255, 0.7)',
                    'rgba(255, 159, 64, 0.7)'
                ],
                borderColor: [
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 99, 132, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                },
                title: {
                    display: false
                }
            }
        }
    });
    <?php endforeach; ?>
</script>