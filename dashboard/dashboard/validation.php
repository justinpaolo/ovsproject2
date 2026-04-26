<?php
include("../admin/include/conn.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voting Results - Voting System</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            scroll-behavior: smooth;
            font-family: 'Poppins', sans-serif;
        }
        .navbar {
            background-color: #74b9ff;
            padding: 0;
        }
        .navbar-brand {
            display: flex;
            align-items: center;
            padding: 0;
            margin: 0;
        }
        .navbar-brand img {
            width: 50px;
            height: 50px;
            margin: 10px;
        }
        .navbar .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .nav-link {
            color: white !important;
            font-weight: 500;
            padding: 20px;
            margin: 0 5px;
        }
        .navbar-nav {
            display: flex;
            align-items: center;
        }
        h1 {
            color: #333;
            font-size: 2.5rem;
            margin: 40px 0;
            font-weight: 600;
        }
        .alert-info {
            background-color: #e3f2fd;
            border: none;
            color: #333;
            padding: 15px;
            border-radius: 5px;
            margin: 20px auto;
            max-width: 800px;
        }
        .footer-links {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-top: 50px;
        }
        .footer-links a {
            color: #74b9ff;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }
        .footer-links a:hover {
            color: #0984e3;
        }
        .footer-links a.active {
            color: #0984e3;
        }
        .table-responsive {
            margin-top: 20px;
        }
        .table th, .table td {
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="image/16.png" alt="PHINMA EDUCATION">
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
                        <a class="nav-link" href="year.php">Candidate</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="result.php">Result</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">About</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <h1 class="text-center">Voting Results</h1>

        <?php 
        try {
            $query = "SELECT position, firstname, lastname, COUNT(votes.candidate_id) as vote_count 
                      FROM candidate 
                      LEFT JOIN votes ON candidate.candidate_id = votes.candidate_id 
                      WHERE candidate.status = 'approved' 
                      GROUP BY candidate.candidate_id 
                      ORDER BY vote_count DESC";
            $stmt = $conn->prepare($query);
            $stmt->execute();

            if($stmt->rowCount() > 0) {
                echo '<div class="table-responsive">';
                echo '<table class="table table-bordered table-striped">';
                echo '<thead>';
                echo '<tr>';
                echo '<th>Position</th>';
                echo '<th>First Name</th>';
                echo '<th>Last Name</th>';
                echo '<th>Votes</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';
                while($row = $stmt->fetch()) {
                    echo '<tr>';
                    echo '<td>' . $row['position'] . '</td>';
                    echo '<td>' . $row['firstname'] . '</td>';
                    echo '<td>' . $row['lastname'] . '</td>';
                    echo '<td>' . $row['vote_count'] . '</td>';
                    echo '</tr>';
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

       
    </div>

    <script src="js/jquery-3.2.1.slim.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>
</html>