<?php
include("../admin/include/conn.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View All Candidates - Voting System</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
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
            box-shadow: 0 8px 20px rgba(115, 140, 115, 0.15);
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
                        <a class="nav-link" href="result.php">Result</a>
                    </li>
                    <!-- <li class="nav-item">
                        <a class="nav-link" href="about.php">About</a>
                    </li> -->
                </ul>
            </div>
        </div>
    </nav>

    <div class="container1">
        <h1>View Candidates</h1>
        <form method="GET" class="mb-4">
            <div class="form-group" style="max-width:320px; margin:0 auto; text-align:center;">
                <label for="election_type" style="display:block; margin-bottom:0.5rem;">Select Election:</label>
                <select name="election_type" id="election_type" class="form-control" onchange="this.form.submit()">
                    <option value="">All Elections</option>
                    <option value="National" <?php if(isset($_GET['election_type']) && $_GET['election_type'] == 'National') echo 'selected'; ?>>National Election</option>
                    <option value="Local" <?php if(isset($_GET['election_type']) && $_GET['election_type'] == 'Local') echo 'selected'; ?>>Local Election</option>
                </select>
            </div>
        </form>
        
        <?php 
        try {
            $election_type = isset($_GET['election_type']) ? $_GET['election_type'] : '';
            $query = "SELECT * FROM candidate WHERE status = 'approved'";
            if ($election_type) {
                $query .= " AND election_type = :election_type";
            }
            $stmt = $conn->prepare($query);
            if ($election_type) {
                $stmt->bindParam(':election_type', $election_type);
            }
            $stmt->execute();

            if($stmt->rowCount() > 0) {
                echo '<div class="table-responsive">';
                echo '<table class="table table-bordered table-striped">';
                echo '<thead>';
                echo '<tr>';
                echo '<th>ID</th>'; 
                echo '<th>Image</th>';
                echo '<th>First Name</th>';
                echo '<th>Last Name</th>';
                echo '<th>Position</th>';
                echo '<th>Year Level</th>';
                echo '<th>Gender</th>';
                echo '<th>Party</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';
                while($row = $stmt->fetch()) {
                    echo '<tr>';
                    echo '<td>' . $row['candidate_id'] . '</td>';
                    if (!empty($row['img'])) {
                        echo '<td><img src="../admin/uploads/' . htmlspecialchars($row['img']) . '" class="candidate-img" alt="Candidate"></td>';
                    } else {
                        echo '<td><img src="../admin/uploads/default-profile.png" class="candidate-img" alt="No Photo"></td>';
                    }
                    echo '<td>' . htmlspecialchars($row['firstname']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['lastname']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['position']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['grade_level']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['gender']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['party']) . '</td>';
                    echo '</tr>';
                }
                echo '</tbody>';
                echo '</table>';
                echo '</div>';
            } else {
                ?>
                <div class="alert alert-info text-center">
                    No candidates found at this time.
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
    </div>

    <script src="js/jquery-3.2.1.slim.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>
</html>