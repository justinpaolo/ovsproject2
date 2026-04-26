<?php
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VP-INTERNAL Candidates - Voting System</title>
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
        .card {
            margin: 20px auto;
            max-width: 300px;
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card img {
            width: 100%;
            height: 300px;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }
        .card-body {
            text-align: center;
        }
        .card-title {
            font-size: 1.5rem;
            font-weight: 600;
        }
        .card-text {
            font-size: 1rem;
            color: #555;
        }
        .btn-vote {
            background-color: #74b9ff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .btn-vote:hover {
            background-color: #0984e3;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            
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
        <h1 class="text-center">VP-INTERNAL</h1>
        
        <div class="row">
        <?php 
        try {
            $query = "SELECT * FROM candidate WHERE position = 'VP-INTERNAL' AND status = 'approved'";
            $stmt = $conn->prepare($query);
            $stmt->execute();

            if($stmt->rowCount() > 0) {
                while($row = $stmt->fetch()) {
                    echo '<div class="col-md-4">';
                    echo '<div class="card">';
                    echo '<img src="../admin/uploads/' . $row['img'] . '" class="card-img-top" alt="Candidate">';
                    echo '<div class="card-body">';
                    echo '<h5 class="card-title">' . $row['firstname'] . ' ' . $row['lastname'] . '</h5>';
                    echo '<p class="card-text">' . $row['grade_level'] . '</p>';
                    echo '<p class="card-text">' . $row['gender'] . '</p>';
                    echo '<p class="card-text">' . $row['party'] . '</p>';

                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                ?>
                <div class="alert alert-info text-center">
                    No VP-Internal candidates found at this time.
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

        <div class="footer-links">
            <a href="president.php">President</a>
            <a href="vp_internal.php" class="active">VP-Internal</a>
            <a href="vp_external.php">VP-External</a>
            <a href="confirmation.php">Vote Now</a>
        </div>
    </div>

    <script src="js/jquery-3.2.1.slim.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>
</html>