<?php

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

include('../security.php');
include('../admin/include/conn.php');

// Check voting status
try {
    $query = "SELECT voting_status FROM settings WHERE id = 1";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $voting_status = $stmt->fetchColumn();
} catch (PDOException $e) {
    $voting_status = 0; // Default to disabled
}

// Get voter ID from session (adjust if your session variable is different)
$voter_id = $_SESSION['voter_id'] ?? $_SESSION['google_email'] ?? null;
if (!$voter_id) {
    // Redirect to login or show error
}

// Check if voter has already voted
$query = "SELECT COUNT(*) FROM votes WHERE voter_id = :voter_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':voter_id', $voter_id);
$stmt->execute();
$hasVoted = $stmt->fetchColumn() > 0;
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

<style>
    body {
    background: linear-gradient(135deg, #e2ecec, #c8d5b9, #a4c6a4);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
    padding: 0;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

.election-btn {
    display:inline-block;
    font-size:1.1rem;
    font-weight:600;
    color:#fff;
    background:#355c36;
    border:none;
    border-radius:8px;
    padding:8px 24px;
    margin-right:16px;
    box-shadow:0 2px 8px rgba(115,140,115,0.10);
    cursor:pointer;
    transition:background 0.2s, transform 0.2s;
}
.election-btn:last-child {
    margin-right:0;
}
.election-btn:hover {
    background:#4a6a4a;
    transform:scale(1.05);
}

/* Navbar styles */
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

/* Container wrapper similar to login container */
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

/* Header text */
.container1 h1 {
    color: #4a6a4a;
    font-weight: 700;
    margin-bottom: 30px;
    text-transform: uppercase;
    letter-spacing: 2px;
}

/* Image styles */
.container1 img {
    max-width: 100%;
    height: auto;
    border-radius: 12px;
    border: 1.5px solid #c3d8cc;
    box-shadow: 0 4px 12px rgba(107, 142, 107, 0.2);
    margin-bottom: 30px;
}

/* SlideUpBtn - Update colors to pastel green theme */
.slideUpBtn {
    padding: 14px 28px;
    background-color: transparent;
    border: 2px solid #6b8e6b;
    border-radius: 10px;
    position: relative;
    overflow: hidden;
    font-weight: 600;
    font-size: 1rem;
    color: #4a6a4a;
    cursor: pointer;
    transition: all 0.5s cubic-bezier(1,.15,.34,.92);
}

.slideUpBtn span {
    display: inline-block;
    transition: inherit;
    color: #4a6a4a;
}

.slideUpBtn:hover span {
    opacity: 0;
    transform: translateY(-100%);
}

.slideUpBtn::before {
    content: "";
    background: linear-gradient(45deg, #8fbf8f, #6b8e6b);
    height: 100%;
    left: 0;
    position: absolute;
    top: 0;
    transform: translateY(100%);
    transition: transform 0.5s cubic-bezier(1,.15,.34,.92);
    width: 100%;
    z-index: -1;
}

.slideUpBtn::after {
    content: 'Vote Now';
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    width: 100%;
    position: absolute;
    top: 0;
    left: 0;
    color: #f0f5f2;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1.8px;
    transform: translateY(100%);
    transition: inherit;
    z-index: 1;
}

.slideUpBtn:hover::before {
    transform: translateY(0) scale(3);
    transition-delay: 0.025s;
}

.slideUpBtn:hover::after {
    opacity: 1;
    transform: translateY(0);
}

/* Footer like spacing if you want */
footer {
    margin-top: auto;
    padding: 20px 0;
    text-align: center;
    color: #4a6a4a;
    font-weight: 600;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .container1 {
        width: 90%;
        padding: 25px 20px;
    }

    .navbar .nav-link {
        padding: 15px 8px !important;
        font-size: 16px;
    }
}

</style>

</head>
<body>

<!-- Fixed Navbar Design -->
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
                <li class="nav-item">
                  <a class="nav-link" href="view_candidates.php">View Candidates</a>
                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="profile.php">
                                        <?php if (!empty(
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                        
                                                $_SESSION['picture'] ?? ''
                                        )): ?>
                                            <img src="<?php echo htmlspecialchars($_SESSION['picture']); ?>" alt="avatar" style="width:30px;height:30px;border-radius:50%;object-fit:cover;margin-right:8px;">
                                        <?php endif; ?>
                                        Profile
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="../index.php">Logout</a>
                                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container1">
    <div class="row">
        <!-- <div class="col-md-12" style="margin-bottom: 18px; text-align:center;">
            <button class="election-btn" onclick="window.location.href='national_election.php'">National Election</button>
            <button class="election-btn" onclick="window.location.href='llocal_election.php'">Local Election</button>
        </div> -->
        <div class="col-md-12"> 
            <h1 style="margin-bottom: 20px;">PHINMA UNIVERSITY OF ILOILO</h1>
        </div>
    <div class="col-md-12" style=" width: 50%; ">
      <img src="image/13.jpg" alt="" srcset="" height="400vh " width="50%">
    </div>
    <div class="col-md-12">
      <?php if ($hasVoted): ?>
        <div class="alert alert-warning mt-4" role="alert">
          You have already voted. You cannot vote again.
        </div>
      <?php elseif ($voting_status != 1): ?>
        <div class="alert alert-info mt-4" role="alert">
          Voting is currently closed. Please check back later.
        </div>
      <?php else: ?>
        <a href="national_election.php"><button style="margin-top: 20px;" class="slideUpBtn">Vote Now</button></a>
      <?php endif; ?>
    </div>  
  </div>
</div>

</body>
</html>