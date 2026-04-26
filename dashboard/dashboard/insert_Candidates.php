<?php
session_start();

include('../admin/include/conn.php');

if(isset($_POST['submitbtn'])) {
    $id_number = $_POST['id_number'];
    $position = $_POST['position'];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $grade_level = $_POST['grade_level'];
    $course = $_POST['course'];
    $gender = $_POST['gender'];
    $party = $_POST['party'];
    $photo = $_FILES['photo']['name'];
    
    // Check if the id_number exists in the voters table with the role 'student'
    $studentQuery = "SELECT * FROM voters WHERE id_number = :id_number AND role = 'student'";
    $studentStmt = $conn->prepare($studentQuery);
    $studentStmt->execute([':id_number' => $id_number]);
    $student = $studentStmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        $_SESSION['status'] = "No student found with the provided ID number.";
        header('Location: candidate.php');
        exit();
    }

    if($photo != "") {
        $target = "uploads/".basename($_FILES['photo']['name']);
        if (!move_uploaded_file($_FILES['photo']['tmp_name'], $target)) {
            $_SESSION['status'] = "Failed to upload image.";
            header('Location: candidate.php');
            exit();
        }
    }
    
    // Check if id_number already exists in the candidate table
    $checkQuery = "SELECT * FROM candidate WHERE id_number = :id_number";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->execute([':id_number' => $id_number]);
    if ($checkStmt->rowCount() > 0) {
        $_SESSION['status'] = "You can only apply once!!!.";
        header('Location: ../index.php');
        exit();
    }

    // Check the total number of candidates for the specific position
    $positionsToCheck = ['PRESIDENT', 'VP-INTERNAL', 'VP-EXTERNAL'];
    if (in_array($position, $positionsToCheck)) {
        $countQuery = "SELECT COUNT(*) as total FROM candidate WHERE position = :position";
        $countStmt = $conn->prepare($countQuery);
        $countStmt->execute([':position' => $position]);
        $countResult = $countStmt->fetch(PDO::FETCH_ASSOC);

        if ($countResult['total'] >= 2) {
            $_SESSION['status'] = "Only 2 person can apply for this position.";
            header('Location: ../index.php');
            exit();
        }
    }
    
    try {
        $query = "INSERT INTO candidate (id_number, position, firstname, lastname, grade_level, course, gender, img, party, status, date_registered) 
                 VALUES (:id_number, :position, :fname, :lname, :grade_level, :course, :gender, :photo, :party, 'pending', NOW())";
        $stmt = $conn->prepare($query);
        $stmt->execute([
            ':id_number' => $id_number,
            ':position' => $position,
            ':fname' => $fname,
            ':lname' => $lname,
            ':grade_level' => $grade_level,
            ':course' => $course,
            ':gender' => $gender,
            ':party' => $party,
            ':photo' => $photo
        ]);
        
        $_SESSION['success'] = "Candidate Added Successfully";
    } catch(PDOException $e) {
        $_SESSION['status'] = "Failed to Add Candidate: " . $e->getMessage();
    }
    
    header('Location: ../index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidate Enrollment</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 50px;
        }
        .form-container {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-container h1 {
            margin-bottom: 30px;
            font-size: 24px;
            font-weight: 600;
            color: #333;
        }
        .form-container .form-group {
            margin-bottom: 20px;
        }
        .form-container .form-control {
            border-radius: 5px;
            height: 45px;
        }
        .form-container .btn {
            background-color: #007bff;
            color: #fff;
            border-radius: 5px;
            padding: 10px 20px;
            font-size: 16px;
            font-weight: 600;
        }
        .form-container .btn:hover {
            background-color: #0056b3;
        }
        .form-container .custom-file-label::after {
            content: "Browse";
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="form-container">
                    <h1>Candidate Enrollment</h1>
                    <form action="insert_Candidates.php" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="txtName">First Name</label>
                            <input type="text" class="form-control" id="txtName" name="fname" required>
                        </div>
                        <div class="form-group">
                            <label for="txtLastName">Last Name</label>
                            <input type="text" class="form-control" id="txtLastName" name="lname" required>
                        </div>
                        <div class="form-group">
                            <label for="txtbranch">Year Level</label>
                            <select class="form-control" id="txtbranch" name="grade_level" required>
                                <option>1st Year</option>
                                <option>2nd Year</option>
                                <option>3rd Year</option>
                                <option>4th Year</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="txtbranch">Course</label>
                            <select class="form-control" id="txtbranch" name="course" required>
                                <option>Information Technology</option>
                                <option>Computer Science</option>
                                <option>Civil</option>
                                <option>Mechanical</option>
                                <option>Electrical</option>
                                <option>Metallurgy</option>
                                <option>Electronics and Telecom.</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="gender">Gender</label>
                            <select class="form-control" id="gender" name="gender" required>
                                <option>Male</option>
                                <option>Female</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="party">Select Party</label>
                            <select class="form-control" id="party" name="party" required>
                                <option>kusog sang pamatan-on (KSP)</option>
                                <option>Student Democratic Alliance</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="role">Select Position</label>
                            <select class="form-control" id="role" name="position" required>
                                <option>PRESIDENT</option>
                                <option>VP-INTERNAL</option>
                                <option>VP-EXTERNAL</option>
                                <option>SECRETARY</option>
                                <option>TREASURER</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="id_number">ID Number</label>
                            <input type="text" class="form-control" id="id_number" name="id_number" required>
                        </div>
                        <div class="form-group">
                            <label for="image">Image</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="image" name="photo" accept="image/*" required>
                                <label class="custom-file-label" for="image">Choose file</label>
                            </div>
                        </div>
                        <button type="submit" name="submitbtn" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Add the following code if you want the name of the file appear on select
        $(".custom-file-input").on("change", function() {
            var fileName = $(this).val().split("\\").pop();
            $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
        });
    </script>
</body>
</html>
