<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Register - VOTE UI</title>
    <link href="admin/css/sb-admin-2.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <style>
        /* Soft pastel green gradient background blending with the card */
        body {
            background: linear-gradient(135deg, #e2ecec, #c8d5b9, #a4c6a4);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
        }

        /* Card styling with soft pale green background and subtle shadows */
        .card {
            background: #e6f0ea; /* soft pale green */
            border: 1.5px solid #c3d8cc; /* muted green border */
            border-radius: 20px;
            box-shadow: 0 8px 20px rgba(115, 140, 115, 0.15);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 28px rgba(115, 140, 115, 0.25);
        }

        /* Header Section */
        #headerSection {
            padding: 2rem;
            text-align: center;
        }

        #headerSection h1 {
            color: #4a6a4a; /* darker muted green */
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 20px;
            /* subtle gradient text */
            background: linear-gradient(45deg, #7ca27c, #4a6a4a);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Form controls with soft backgrounds and muted green borders */
        .form-control {
            background: #f0f5f2 !important; /* very light green */
            border: 1.5px solid #b8c8b8 !important; /* muted border */
            border-radius: 10px !important;
            padding: 12px !important;
            color: #4a6a4a !important;
            margin-bottom: 15px !important;
            transition: all 0.3s ease !important;
        }

        .form-control:focus {
            background: #e1ede4 !important;
            border-color: #7ca27c !important;
            box-shadow: 0 0 8px rgba(124, 162, 124, 0.4) !important;
            color: #3e5a3e !important;
            outline: none !important;
        }

        .form-control::placeholder {
            color: #a5b9a5 !important;
        }

        /* Select dropdown arrow and options */
        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%234a6a4a' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 1em;
            font-size: 1rem !important;
            padding: 12px !important;
            height: auto !important;
            width: 100% !important;
            min-width: 0 !important;
            box-sizing: border-box !important;
        }

        select.form-control option {
            background-color: #d5e8d4; /* soft pastel green */
            color: #4a6a4a;
            font-size: 1rem;
            padding: 8px;
        }

        /* Register button with gentle green gradient */
        .btn-register {
            background: linear-gradient(45deg, #8fbf8f, #6b8e6b) !important;
            border: none !important;
            border-radius: 10px !important;
            padding: 12px !important;
            font-weight: 600 !important;
            text-transform: uppercase !important;
            letter-spacing: 2px !important;
            transition: all 0.3s ease !important;
            color: #f0f5f2 !important;
            width: 100%;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(107, 142, 107, 0.35);
        }

        .btn-register:hover {
            background: linear-gradient(45deg, #a3cca3, #7ca27c) !important;
            box-shadow: 0 6px 18px rgba(124, 162, 124, 0.5) !important;
            transform: translateY(-2px);
        }

        /* Text headings */
        .text-gray-900 {
            color: #4a6a4a !important;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        /* Alerts with pastel backgrounds */
        .alert {
            background: #d5e8d4; /* soft pastel green */
            border: 1px solid #b7d7b1;
            color: #3c763d;
            border-radius: 8px;
            padding: 12px 18px;
            margin-bottom: 20px;
            font-weight: 500;
            font-size: 0.95rem;
            box-shadow: none;
        }

        .alert-danger {
            background: #f9d6d5; /* soft pastel red */
            color: #a94442;
            border: 1px solid #e4b9b8;
        }

        /* Small links */
        .small {
            color: #7ca27c;
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        .small:hover {
            color: #a3cca3;
            text-decoration: underline;
        }

        hr {
            border-color: #c3d8cc;
        }
    </style>
</head>
<body>
    <div id="headerSection">
        <h1>Vote UI</h1>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-6 col-md-8">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h1 class="h4 text-gray-900">Create an Account</h1>
                        </div>

                        <?php
                        if(isset($_SESSION['status']) && $_SESSION['status'] != '') {
                            echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['status'], ENT_QUOTES, 'UTF-8') . '</div>';
                            unset($_SESSION['status']);
                        }
                        if(isset($_SESSION['success']) && $_SESSION['success'] != '') {
                            echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8') . '</div>';
                            unset($_SESSION['success']);
                        }
                        ?>

                        <form class="user" action="process.php" method="POST">
                            <div class="form-group">
                                <label for="id_number">ID Number</label>
                                <input type="number" name="id_number" id="id_number" class="form-control" placeholder="ID Number" required>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-6 mb-3 mb-sm-0">
                                    <label for="firstname">Firstname</label>
                                    <input type="text" name="firstname" id="firstname" class="form-control" placeholder="First Name" required>
                                </div>
                                <div class="col-sm-6">
                                    <label for="lastname">Lastname</label>
                                    <input type="text" name="lastname" id="lastname" class="form-control" placeholder="Last Name" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-6 mb-3 mb-sm-0">
                                    <label for="gender">Gender</label>
                                    <select name="gender" id="gender" class="form-control" required>
                                        <option value=""></option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <label for="program">Program</label>
                                    <select name="program" id="program" class="form-control" required>
                                        <option value=""></option>
                                        <option value="BSIT">BSIT</option>
                                        <option value="BSCS">BSBA</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-6 mb-3 mb-sm-0">
                                    <label for="year_level">Year Level</label>
                                    <select name="year_level" id="year_level" class="form-control" required>
                                        <option value=""></option>
                                        <option value="1st Year">1st Year</option>
                                        <option value="2nd Year">2nd Year</option>
                                        <option value="3rd Year">3rd Year</option>
                                        <option value="4th Year">4th Year</option>
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <label for="account">Role:</label>
                                    <select name="account" id="account" class="form-control" required>
                                        <option value="Voter">Voter</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-6 mb-3 mb-sm-0">
                                    <label for="password">Password</label>
                                    <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
                                </div>
                                <div class="col-sm-6">
                                    <label for="cpassword">Confirm Password</label>
                                    <input type="password" name="cpassword" id="cpassword" class="form-control" placeholder="Confirm Password" required>
                                </div>
                            </div>
                            <button type="submit" name="register_btn" class="btn btn-register btn-block">
                                Register Account
                            </button>
                        </form>

                        <hr>

                        <div class="text-center">
                            <p>Already have an account? <a class="small" href="index.php">Login here!</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="admin/js/bootstrap.bundle.min.js"></script>
</body>
</html>
