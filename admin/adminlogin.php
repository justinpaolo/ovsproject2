<?php
session_start();
include('include/conn.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        $query = "SELECT * FROM admin WHERE username = :username LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->execute([':username' => $username]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin) {
            // Debug: Check if the username exists
            echo "<script>console.log('Username found: " . $admin['username'] . "');</script>";

            // Verify the password
            if (password_verify($password, $admin['password'])) {
                // Set admin_id in session  
                $_SESSION['admin_id'] = $admin['admin_id'];
                echo ("<script>
                alert('Welcome Admin');
                window.location.href = 'index.php';
                </script>");
                
            } else {
                echo "<script>
                        alert('Invalid password.');
                        window.location.href = 'adminlogin.php';
                      </script>";
            }
        } else {
            echo "<script>
                    alert('Invalid username.');
                    window.location.href = 'adminlogin.php';
                  </script>";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Admin Login</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Quicksand', sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            /* Soft pastel green gradient background */
            background: linear-gradient(135deg, #e2ecec, #c8d5b9, #a4c6a4);
            margin: 0;
        }

        .signin {
            width: 400px;
            background: #e6f0ea; /* soft pale green */
            border: 1.5px solid #c3d8cc; /* muted green border */
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(115, 140, 115, 0.15);
            padding: 40px 30px;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        .signin:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 28px rgba(115, 140, 115, 0.25);
        }

        .logo-container {
            margin-bottom: 25px;
            text-align: center;
        }

        .logo-container img {
            max-width: 150px;
        }

        h2 {
            color: #4a6a4a; /* muted dark green */
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.8px;
            margin-bottom: 30px;
        }

        form.form {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .inputBox {
            position: relative;
            width: 100%;
        }

        .inputBox input[type="text"],
        .inputBox input[type="password"] {
            width: 100%;
            background: #f0f5f2; /* very light green */
            border: 1.5px solid #b8c8b8; /* muted border */
            border-radius: 10px;
            padding: 15px;
            color: #4a6a4a;
            font-size: 1em;
            transition: background 0.3s ease, border-color 0.3s ease;
            outline: none;
        }

        .inputBox input[type="text"]:focus,
        .inputBox input[type="password"]:focus {
            background: #e1ede4;
            border-color: #7ca27c;
            box-shadow: 0 0 8px rgba(124, 162, 124, 0.4);
            color: #3e5a3e;
        }

        .inputBox input[type="submit"] {
            background: linear-gradient(45deg, #8fbf8f, #6b8e6b);
            border: none;
            border-radius: 10px;
            padding: 15px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.8px;
            color: #f0f5f2;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(107, 142, 107, 0.35);
            transition: all 0.3s ease;
        }

        .inputBox input[type="submit"]:hover {
            background: linear-gradient(45deg, #a3cca3, #7ca27c);
            box-shadow: 0 6px 18px rgba(124, 162, 124, 0.5);
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="signin">
        <div class="logo-container">
            <img src="img/PHINMA-Ed-Logo.png" alt="PHINMA Education Logo" />
        </div>
        <h2>Admin Login</h2>
        <form class="form" action="adminlogin.php" method="POST">
            <div class="inputBox">
                <input type="text" name="username" placeholder="Username" required />
            </div>
            <div class="inputBox">
                <input type="password" name="password" placeholder="Password" required />
            </div>
            <div class="inputBox">
                <input type="submit" name="adminbtn" value="Login" />
            </div>
        </form>
    </div>
</body>
</html>
