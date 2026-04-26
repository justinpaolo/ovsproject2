<?php
include('security.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login - VOTE UI</title>
    <link href="admin/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <style>
        /* Soft pastel green gradient background blending with login box */
        body {
            background: linear-gradient(135deg, #e2ecec, #c8d5b9, #a4c6a4);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
        }

        /* Container with subtle shadow and soft borders */
        .login-container {
            background: #e6f0ea; /* soft pale green */
            border: 1.5px solid #c3d8cc; /* muted green border */
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(115, 140, 115, 0.15);
            padding: 38px 36px;
            max-width: 400px;
            width: 90%;
            transition: all 0.3s ease;
            text-align: center;
        }

        .login-container:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 28px rgba(115, 140, 115, 0.25);
        }

        /* Logo container */
        .logo-container {
            margin-bottom: 25px;
        }

        .logo-container img {
            max-width: 150px;
            width: 100%;
            height: auto;
            display: block;
            margin: 0 auto;
            object-fit: contain;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 20px;
                max-width: 340px;
            }
            .logo-container img {
                max-width: 110px;
            }
        }

        /* Header icon and text with calm green */
        .login-header {
            margin-bottom: 38px;
        }

        .login-header i {
            font-size: 48px;
            color: #6b8e6b; /* muted green */
            margin-bottom: 18px;
        }

        .login-header h4 {
            color: #4a6a4a; /* darker muted green */
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.8px;
            margin: 0;
        }

        /* Inputs with soft backgrounds and subtle borders */
        .form-control {
            background: #f0f5f2; /* very light green */
            border: 1.5px solid #b8c8b8; /* muted border */
            border-radius: 10px;
            padding: 14px 15px;
            color: #4a6a4a;
            margin-bottom: 18px;
            font-size: 1rem;
            transition: background 0.3s ease, border-color 0.3s ease;
        }

        .form-control:focus {
            background: #e1ede4;
            border-color: #7ca27c;
            box-shadow: 0 0 8px rgba(124, 162, 124, 0.4);
            color: #3e5a3e;
            outline: none;
        }

        .form-control::placeholder {
            color: #a5b9a5;
        }

        /* Buttons with gentle green gradient and subtle shadow */
        .btn-login {
            background: linear-gradient(45deg, #8fbf8f, #6b8e6b);
            border: none;
            border-radius: 10px;
            padding: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.8px;
            width: 100%;
            margin-top: 18px;
            color: #f0f5f2;
            box-shadow: 0 4px 12px rgba(107, 142, 107, 0.35);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .btn-login:hover {
            background: linear-gradient(45deg, #a3cca3, #7ca27c);
            box-shadow: 0 6px 18px rgba(124, 162, 124, 0.5);
            transform: translateY(-2px);
        }

        /* Alert boxes with soft pastel backgrounds */
        .alert {
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

        .alert-success {
            background: #d5e8d4; /* soft pastel green */
            color: #3c763d;
            border: 1px solid #b7d7b1;
        }

        /* Close button color */
        .close {
            color: #4a6a4a;
            opacity: 1;
            font-size: 1.1rem;
        }

        /* Register links with muted green */
        .register-link {
            text-align: center;
            margin-top: 22px;
            font-size: 0.95rem;
            color: #4a6a4a;
        }

        .register-link a {
            color: #7ca27c;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .register-link a:hover {
            color: #a3cca3;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo-container">
            <!-- Use relative path (works when site is in subfolder); fallback to placeholder -->
            <img
                src="admin/img/PHINMA-Ed-Logo.png"
                alt="PHINMA Education Logo"
                onerror="this.onerror=null; this.src='admin/img/placeholder.png';"
            />
        </div>

        <div class="login-header">
            <!-- <i class="fas fa-vote-yea"></i> -->
            <h4>Login using Phinmaed Email</h4>
        </div>

        <?php
        if (isset($_SESSION['status']) && $_SESSION['status'] != '') {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">'
                . htmlspecialchars($_SESSION['status'], ENT_QUOTES, 'UTF-8') .
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>';
            unset($_SESSION['status']);
        }

        if (isset($_SESSION['success']) && $_SESSION['success'] != '') {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">'
                . htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8') .
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>';
            unset($_SESSION['success']);
        }
        ?>

        <!-- <form action="login_process.php" method="POST">
            <div class="form-group">
                <input type="text" name="id_number" class="form-control" placeholder="ID Number" required />
            </div>
            <div class="form-group">
                <input type="password" name="password" class="form-control" placeholder="Password" required />
            </div>
            <div class="form-group">
                <select name="role" class="form-control" required>
                    <option value="voter">Voter</option>
                    <option value="candidate">Candidate</option>
                </select>
            </div>
            <button type="submit" name="login_btn" class="btn btn-login">Login</button>
        </form> -->

            <div style="margin: 20px 0;">
                <div id="g_id_onload"
                    data-client_id="463011006005-c0qi12rirk8ge31pul5egpnok83tdd3k.apps.googleusercontent.com"
                    data-context="signin"
                    data-ux_mode="redirect"
                    data-login_uri="http://localhost/ovsproject2/google_login_process.php"
                    data-auto_prompt="false">
                </div>
                <div class="g_id_signin" data-type="standard" data-shape="rectangular" data-theme="outline" data-text="sign_in_with" data-size="large" data-logo_alignment="left"></div>
            </div>

            <form id="googleLoginForm" action="google_login_process.php" method="POST" style="display:none;">
                <input type="hidden" name="credential" id="googleCredential" />
            </form>

        <div class="register-link">
            <!-- <p>Don't have an account? <a href="register.php">Register here</a></p> -->
            <a href="admin/adminlogin.php">Login as Admin</a>
        </div>
    </div>

    <script src="admin/js/jquery.min.js"></script>
    <script src="admin/js/bootstrap.bundle.min.js"></script>

        <!-- Google Sign-In JS -->
        <script src="https://accounts.google.com/gsi/client" async defer></script>
        <script>
            function handleCredentialResponse(response) {
                document.getElementById('googleCredential').value = response.credential;
                document.getElementById('googleLoginForm').submit();
            }
        </script>
    <script>
        $(document).ready(function () {
            <?php
            if (isset($_GET['login']) && $_GET['login'] == 'success') {
                echo "$('#successModal').modal('show');";
            }
            if (isset($_GET['login']) && $_GET['login'] == 'failed') {
                echo "$('#errorModal').modal('show');";
            }
            ?>
        });
    </script>
</body>
</html>
