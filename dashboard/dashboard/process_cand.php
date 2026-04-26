<?php
include("../admin/include/conn.php");

// Check if form was submitted
if(!isset($_POST['txtRollNo'])) {
    die("<script>alert('Form not submitted properly.'); window.location='register_candidate.php';</script>");
}

// Debug: Print POST data
echo "<pre>POST Data: ";
print_r($_POST);
echo "</pre>";

// Debug: Print FILES data
echo "<pre>FILES Data: ";
print_r($_FILES);
echo "</pre>";

// Initialize variables to prevent undefined array key errors
$txtRollNo = isset($_POST['txtRollNo']) ? $_POST['txtRollNo'] : '';
$firstname = isset($_POST['firstname']) ? $_POST['firstname'] : '';
$lastname = isset($_POST['lastname']) ? $_POST['lastname'] : '';
$position = isset($_POST['position']) ? $_POST['position'] : '';
$grade_level = isset($_POST['grade_level']) ? $_POST['grade_level'] : '';
$gender = isset($_POST['gender']) ? $_POST['gender'] : '';
$party = isset($_POST['party']) ? $_POST['party'] : '';

// Validate required fields
if(empty($txtRollNo) || empty($firstname) || empty($lastname) || empty($position)) {
    die("<script>alert('Please fill in all required fields.'); window.location='register_candidate.php';</script>");
}

// Handle file upload
$img = '';
if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    $target_dir = "../admin/uploads/";
    
    // Create directory if it doesn't exist
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    
    // Debug: Print file information
    echo "Target file: " . $target_file . "<br>";
    echo "File type: " . $imageFileType . "<br>";
    
    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if($check !== false) {
        // Allow certain file formats
        if($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg") {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $img = basename($_FILES["image"]["name"]);
                echo "File uploaded successfully: " . $img . "<br>";
            } else {
                echo "Error uploading file.<br>";
            }
        } else {
            echo "Invalid file type. Only JPG, JPEG & PNG files are allowed.<br>";
        }
    } else {
        echo "File is not an image.<br>";
    }
}

try {
    // Debug: Print connection status
    echo "Database connection status: Connected<br>";
    
    // Create table if it doesn't exist
    $createTable = "CREATE TABLE IF NOT EXISTS candidate (
        candidate_id INT AUTO_INCREMENT PRIMARY KEY,
        txtRollNo VARCHAR(50),
        firstname VARCHAR(100),
        lastname VARCHAR(100),
        position VARCHAR(50),
        grade_level VARCHAR(50),
        gender VARCHAR(20),
        party VARCHAR(100),
        img VARCHAR(255),
        status VARCHAR(20) DEFAULT 'pending'
    )";
    $conn->exec($createTable);
    echo "Table check/creation completed<br>";

    // Insert data
    $sql = "INSERT INTO candidate (txtRollNo, firstname, lastname, position, grade_level, gender, party, img) 
            VALUES (:txtRollNo, :firstname, :lastname, :position, :grade_level, :gender, :party, :img)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':txtRollNo', $txtRollNo);
    $stmt->bindParam(':firstname', $firstname);
    $stmt->bindParam(':lastname', $lastname);
    $stmt->bindParam(':position', $position);
    $stmt->bindParam(':grade_level', $grade_level);
    $stmt->bindParam(':gender', $gender);
    $stmt->bindParam(':party', $party);
    $stmt->bindParam(':img', $img);
    
    // Debug: Print query data
    echo "About to execute query with data:<br>";
    echo "Roll No: $txtRollNo<br>";
    echo "Name: $firstname $lastname<br>";
    echo "Position: $position<br>";
    
    if($stmt->execute()) {
        echo "<script>alert('Candidate registration successful!'); window.location='register_candidate.php';</script>";
    } else {
        echo "<script>alert('Database insertion failed. Please try again.'); window.location='register_candidate.php';</script>";
    }
} catch(PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "<br>";
    echo "<script>alert('Error: " . $e->getMessage() . "'); window.location='register_candidate.php';</script>";
}
?>

