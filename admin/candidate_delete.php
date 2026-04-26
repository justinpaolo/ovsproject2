<?php

include("include/conn.php");

if(isset($_POST['deletebtn'])) {
    $id = $_POST['delete_id'];
    
    try {
        $query = "DELETE FROM candidate WHERE candidate_id = :id";
        $stmt = $conn->prepare($query);
        $stmt->execute([':id' => $id]);
        
        $_SESSION['success'] = "Candidate Deleted Successfully";
    } catch(PDOException $e) {
        $_SESSION['status'] = "Failed to Delete Candidate";
    }
}

header('Location: candidate.php');
exit();
?>