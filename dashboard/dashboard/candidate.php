<?php
include("../admin/include/conn.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Candidates - Voting System</title>
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
        .btn-vote {
            background-color: #0984e3;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .btn-vote:hover {
            background-color: #74b9ff;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="#">
                
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
        <h1 class="text-center">All Candidates</h1>

        <form method="GET" class="mb-4">
            <div class="form-group">
                <label for="position">Select Position:</label>
                <select name="position" id="position" class="form-control" onchange="this.form.submit()">
                    <option value="">All Positions</option>
                    <option value="President" <?php if(isset($_GET['position']) && $_GET['position'] == 'President') echo 'selected'; ?>>President</option>
                    <option value="VP-Internal" <?php if(isset($_GET['position']) && $_GET['position'] == 'VP-Internal') echo 'selected'; ?>>VP-Internal</option>
                    <option value="VP-External" <?php if(isset($_GET['position']) && $_GET['position'] == 'VP-External') echo 'selected'; ?>>VP-External</option>
                </select>
            </div>
        </form>
        
        <?php 
        try {
            $position = isset($_GET['position']) ? $_GET['position'] : '';
            $query = "SELECT * FROM candidate WHERE status = 'approved'";
            if ($position) {
                $query .= " AND position = :position";
            }
            $stmt = $conn->prepare($query);
            if ($position) {
                $stmt->bindParam(':position', $position);
            }
            $stmt->execute();

            if($stmt->rowCount() > 0) {
                echo '<div class="table-responsive">';
                echo '<table class="table table-bordered table-striped">';
                echo '<thead>';
                echo '<tr>';
                echo '<th>ID</th>';
                echo '<th>Roll No.</th>'; // Add Roll No. column
                echo '<th>Image</th>';
                echo '<th>First Name</th>';
                echo '<th>Last Name</th>';                
                echo '<th>Position</th>';
                echo '<th>Year Level</th>';
                echo '<th>Gender</th>';
                echo '<th>Party</th>';
                echo '<th>Action</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';
                while($row = $stmt->fetch()) {
                    echo '<tr>';
                    echo '<td>' . $row['candidate_id'] . '</td>';
                    echo '<td>'. $row['roll_no'] .'</td>';
                    echo '<td><img src="../admin/uploads/' . $row['img'] . '" alt="Candidate" style="width: 50px; height: 50px; object-fit: cover;"></td>';
                    echo '<td>' . $row['firstname'] . '</td>';
                    echo '<td>' . $row['lastname'] . '</td>';
                    echo '<td>' . $row['position'] . '</td>';
                    echo '<td>' . $row['grade_level'] . '</td>';
                    echo '<td>' . $row['gender'] . '</td>';
                    echo '<td>' . $row['party'] . '</td>';
                    echo '<td><a href="confirmation.php?id=' . $row['candidate_id'] . '" class="btn btn-vote">Vote</a></td>';
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

        <div class="footer-links">
            <a href="president.php">President</a>
            <a href="vp_internal.php">VP-Internal</a>
            <a href="vp_external.php">VP-External</a>
        </div>
    </div>

    <script src="js/jquery-3.2.1.slim.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>
</html>