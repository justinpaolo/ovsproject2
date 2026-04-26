<?php

include('security.php');
include('include/conn.php');

if (isset($_POST['candidatebtn'])) {
    $election_type = isset($_POST['election_type']) ? $_POST['election_type'] : null;
    $position = $_POST['position'];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $grade_level = $_POST['grade_level'];
    $department = $_POST['department']; 
    $gender = $_POST['gender'];
    $party = $_POST['party'];
    $photo = $_FILES['photo']['name'];

    // Move uploaded file
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($photo);
    move_uploaded_file($_FILES['photo']['tmp_name'], $target_file);

    // Insert query with status set to 'pending'
    $query = "INSERT INTO candidate (election_type, position, firstname, lastname, grade_level, department, gender, party, img, status) 
              VALUES (:election_type, :position, :firstname, :lastname, :grade_level, :department, :gender, :party, :img, 'pending')";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':election_type', $election_type);
    $stmt->bindParam(':position', $position);
    $stmt->bindParam(':firstname', $fname);
    $stmt->bindParam(':lastname', $lname);
    $stmt->bindParam(':grade_level', $grade_level);
    $stmt->bindParam(':department', $department);
    $stmt->bindParam(':gender', $gender);
    $stmt->bindParam(':party', $party);
    $stmt->bindParam(':img', $photo);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Candidate added successfully and is pending approval!";
        header("Location: candidates_approval.php"); // Redirect to candidates_approval.php
        exit();
    } else {
        $_SESSION['status'] = "Candidate not added!";
        header("Location: candidate.php");
        exit();
    }
}

// For updating candidate
if (isset($_POST['updatebtn'])) {
    $id = $_POST['edit_id'];
    $election_type = isset($_POST['edit_election_type']) ? $_POST['edit_election_type'] : null;
    $position = $_POST['edit_position'];
    $firstname = $_POST['edit_fname'];
    $lastname = $_POST['edit_lname'];
    $grade_level = $_POST['edit_grade_level'];
    $department = $_POST['edit_department'];
    $gender = $_POST['edit_gender'];
    $party = $_POST['edit_party'];

    // Handle file upload
    if ($_FILES['edit_photo']['name'] != '') {
        $img_name = $_FILES['edit_photo']['name'];
        $img_tmp_name = $_FILES['edit_photo']['tmp_name'];
        $img_folder = 'uploads/' . $img_name;
        move_uploaded_file($img_tmp_name, $img_folder);

        $query = "UPDATE candidate SET election_type=:election_type, position=:position, firstname=:firstname, lastname=:lastname, grade_level=:grade_level, department=:department, gender=:gender, party=:party, img=:img WHERE candidate_id=:id";
        $stmt = $conn->prepare($query);
        $stmt->execute([
            ':election_type' => $election_type,
            ':position' => $position,
            ':firstname' => $firstname,
            ':lastname' => $lastname,
            ':grade_level' => $grade_level,
            ':department' => $department,
            ':gender' => $gender,
            ':party' => $party,
            ':img' => $img_name,
            ':id' => $id
        ]);
    } else {
        $query = "UPDATE candidate SET election_type=:election_type, position=:position, firstname=:firstname, lastname=:lastname, grade_level=:grade_level, department=:department, gender=:gender, party=:party WHERE candidate_id=:id";
        $stmt = $conn->prepare($query);
        $stmt->execute([
            ':election_type' => $election_type,
            ':position' => $position,
            ':firstname' => $firstname,
            ':lastname' => $lastname,
            ':grade_level' => $grade_level,
            ':department' => $department,
            ':gender' => $gender,
            ':party' => $party,
            ':id' => $id
        ]);
    }

    if ($stmt) {
        $_SESSION['success'] = "Candidate updated successfully";
        header('Location: candidate.php');
    } else {
        $_SESSION['status'] = "Candidate update failed";
        header('Location: candidate.php');
    }
}
?>








