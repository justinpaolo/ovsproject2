<?php
session_start();
include("../admin/include/conn.php");

// Check if voter_id is set in the session
if (!isset($_SESSION['voter_id'])) {
    header("Location: ../index.php");
    exit();
}

$voter_id = $_SESSION['voter_id'];

try {
    $query = "
        SELECT c.election_type, c.position, c.firstname, c.lastname, c.party, c.img 
        FROM votes v
        INNER JOIN candidate c ON v.candidate_id = c.candidate_id
        WHERE v.voter_id = ?
    ";
    $stmt = $conn->prepare($query);
    $stmt->execute([$voter_id]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}

// Separate results into National and Local (or other) groups
$nationalResults = [];
$localResults = [];
foreach ($results as $row) {
    $etype = isset($row['election_type']) ? strtolower($row['election_type']) : '';
    if (strpos($etype, 'national') !== false) {
        $nationalResults[] = $row;
    } else {
        $localResults[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Voter Result</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #e2ecec, #c8d5b9, #a4c6a4); /* pastel green gradient */
            min-height: 100vh;
            margin: 0;
            color: #4a6a4a; /* muted green text */
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: #e6f0ea; /* soft pale green */
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 8px 20px rgba(115, 140, 115, 0.15);
            max-width: 1100px;
            width: 100%;
            color: #4a6a4a;
        }
        .category-title {
            font-size: 28px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 30px;
            color: #3c763d; /* darker muted green */
        }
        .card {
            background: #f0f5f2; /* very light green */
            border: 1.5px solid #b8c8b8;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(107, 142, 107, 0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            text-align: center;
            padding: 20px;
            color: #4a6a4a;
            margin-bottom: 25px;
        }
        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 28px rgba(124, 162, 124, 0.4);
        }
        .card img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #a3cca3;
            margin-bottom: 15px;
        }
        .card-title {
            font-size: 20px;
            font-weight: 700;
            color: #3c763d;
            margin-bottom: 10px;
        }
        .card-text {
            font-size: 16px;
            color: #556b56;
        }
        .no-records {
            font-size: 20px;
            color: #6b8e6b;
            text-align: center;
            margin-top: 40px;
            font-weight: 600;
        }
        .logout-btn {
            margin-top: 30px;
            text-align: center;
        }
        .btn-custom {
            background: linear-gradient(45deg, #8fbf8f, #6b8e6b);
            color: #f0f5f2;
            font-size: 18px;
            font-weight: 700;
            padding: 12px 30px;
            border-radius: 30px;
            border: none;
            transition: background 0.3s ease, transform 0.3s ease;
            cursor: pointer;
            box-shadow: 0 6px 18px rgba(107, 142, 107, 0.45);
        }
        .btn-custom:hover {
            background: linear-gradient(45deg, #a3cca3, #7ca27c);
            transform: scale(1.05);
            box-shadow: 0 8px 24px rgba(124, 162, 124, 0.6);
        }
        @media (min-width: 576px) {
            .row {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
            }
            .col-md-4, .col-sm-6 {
                flex: 0 0 30%;
                max-width: 30%;
                padding: 0 10px;
                box-sizing: border-box;
            }
        }
        /* New layout for election sections */
        .section {
            margin-bottom: 30px;
        }
        .section-heading {
            font-size: 20px;
            color: #2f6f33;
            font-weight: 700;
            margin: 8px 0 18px 6px;
            text-align: left;
        }
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 22px;
            align-items: start;
        }
        .card-wrapper {
            display: flex;
            justify-content: center;
        }
        /* Slightly smaller image for tighter cards */
        .card img {
            width: 110px;
            height: 110px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="category-title">Your Voting Results</h2>
        <div class="section">
            <h3 class="section-heading">National Election</h3>
            <?php if (count($nationalResults) > 0): ?>
                <div class="cards-grid">
                <?php foreach ($nationalResults as $candidate): ?>
                    <div class="card-wrapper">
                        <div class="card">
                            <img src="../admin/uploads/<?php echo !empty($candidate['img']) ? htmlspecialchars($candidate['img']) : 'default.jpg'; ?>" alt="Candidate Image">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo ucwords(htmlspecialchars($candidate['firstname'] . ' ' . $candidate['lastname'])); ?></h5>
                                <p class="card-text">
                                    <strong>Position:</strong> <?php echo htmlspecialchars($candidate['position']); ?><br>
                                    <strong>Party:</strong> <?php echo htmlspecialchars($candidate['party']); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="no-records">No national voting records found.</p>
            <?php endif; ?>
        </div>

        <hr style="margin:30px 0; border:none; border-top:1px solid #d6e6d6;">

        <div class="section">
            <h3 class="section-heading">Local Election</h3>
            <?php if (count($localResults) > 0): ?>
                <div class="cards-grid">
                <?php foreach ($localResults as $candidate): ?>
                    <div class="card-wrapper">
                        <div class="card">
                            <img src="../admin/uploads/<?php echo !empty($candidate['img']) ? htmlspecialchars($candidate['img']) : 'default.jpg'; ?>" alt="Candidate Image">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo ucwords(htmlspecialchars($candidate['firstname'] . ' ' . $candidate['lastname'])); ?></h5>
                                <p class="card-text">
                                    <strong>Position:</strong> <?php echo htmlspecialchars($candidate['position']); ?><br>
                                    <strong>Party:</strong> <?php echo htmlspecialchars($candidate['party']); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="no-records">No local voting records found.</p>
            <?php endif; ?>
        </div>
        <div class="logout-btn">
            <form action="index.php" method="POST">
                <button type="submit" class="btn btn-custom">Go back to dashboard</button>
            </form>
        </div>
    </div>
</body>
</html>
