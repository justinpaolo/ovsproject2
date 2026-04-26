<?php
include('../security.php'); // Include the security file
include("../admin/include/conn.php"); // Include the database connection
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Select Your Election</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #e2ecec, #c8d5b9, #a4c6a4); /* pastel green gradient */
            color: #4a6a4a; /* muted green text */
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            margin-top: 50px;
            background: #e6f0ea; /* soft pale green */
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 8px 20px rgba(115, 140, 115, 0.15);
        }
        .category-container {
            background: #d5e8d4; /* soft pastel green */
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 30px;
            text-align: center;
            font-weight: 600;
            text-transform: uppercase;
            color: #3c763d;
            box-shadow: inset 0 0 10px rgba(124, 162, 124, 0.15);
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
        }
        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 28px rgba(124, 162, 124, 0.4);
        }
        .card img {
            display: block;
            margin: 0 auto 15px auto;
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #a3cca3;
        }
        .card-title {
            font-size: 20px;
            font-weight: 700;
            color: #3c763d;
            margin-bottom: 8px;
        }
        .card-text {
            font-size: 14px;
            color: #556b56;
            margin-bottom: 15px;
        }
        .form-check-label {
            font-weight: 600;
            color: #4a6a4a;
        }
        .btn-submit {
            background: linear-gradient(45deg, #8fbf8f, #6b8e6b);
            color: #f0f5f2;
            font-size: 16px;
            font-weight: 700;
            padding: 12px 30px;
            border-radius: 30px;
            border: none;
            transition: background 0.3s ease, transform 0.3s ease;
            cursor: pointer;
            box-shadow: 0 6px 18px rgba(107, 142, 107, 0.45);
            margin-top: 20px;
        }
        .btn-submit:hover {
            background: linear-gradient(45deg, #a3cca3, #7ca27c);
            transform: scale(1.05);
            box-shadow: 0 8px 24px rgba(124, 162, 124, 0.6);
        }
        .big-election-btn {
            background:#355c36 !important;
            font-weight:700;
            border-radius:12px;
            padding:22px 60px;
            font-size:2rem;
            letter-spacing:1px;
            box-shadow:0 4px 16px rgba(53,92,54,0.15);
            color:#fff !important;
            transition: background 0.3s, transform 0.2s;
        }
        .big-election-btn:hover, .big-election-btn:focus {
            background:linear-gradient(45deg, #4e7c4e, #6b8e6b) !important;
            color:#e6f0ea !important;
            transform: scale(1.07);
            box-shadow:0 8px 32px rgba(53,92,54,0.22);
            text-decoration:none;
        }
        /* Responsive spacing */
        @media (max-width: 767px) {
            .card {
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center" style="color: #3c763d; font-weight: 700; margin-bottom: 40px;">Select Your Election</h1>
        <div class="d-flex justify-content-center mb-4">
            <a href="national_election.php" class="big-election-btn mx-2">National Election</a>
            <a href="local_election.php" class="big-election-btn mx-2">Local Election</a>
        </div>
        <!-- Candidate sections removed as requested -->
    </div>

    <script src="js/jquery-3.2.1.slim.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
document.querySelector('form').addEventListener('submit', function(e) {
    // Get selected candidates for each position
    var positions = ["president", "vp_internal", "vp_external"];
    var summary = "";
    var valid = true;

    positions.forEach(function(pos) {
        var radio = document.querySelector('input[name="' + pos + '"]:checked');
        if (radio) {
            // Find the candidate card
            var card = radio.closest('.card');
            var name = card.querySelector('.card-title').textContent;
            var dept = card.querySelector('.card-text').textContent;
            summary += pos.replace('_', '-').toUpperCase() + ":\n" + name + "\n" + dept + "\n\n";
        } else {
            valid = false;
        }
    });

    if (!valid) {
        alert("Please select a candidate for each position.");
        e.preventDefault();
        return;
    }

    if (!confirm("Please double check your selections:\n\n" + summary + "Are you sure you want to submit your vote?")) {
        e.preventDefault();
    }
});
</script>
</body>
</html>
