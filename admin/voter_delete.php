Failed to delete Voter: SQLSTATE[23000]: Integrity constraint violation: 1451 Cannot delete or update a parent row: a foreign key constraint fails (`ovsproject`.`votes`, CONSTRAINT `votes_ibfk_2` FOREIGN KEY (`voter_id`) REFERENCES `voters` (`voter_id`))<?php
session_start();
include("include/conn.php");

if(isset($_POST['delete_btn'])){
    try{
        $voter_id = $_POST['delete_id'];

        // Delete related votes first
        $query = "DELETE FROM votes WHERE voter_id = :voter_id";
        $stmt = $conn->prepare($query);
        $stmt->execute(['voter_id' => $voter_id]);

        // Then delete the voter
        $query = "DELETE FROM voters WHERE voter_id = :voter_id";
        $stmt = $conn->prepare($query);
        $stmt->execute(['voter_id' => $voter_id]);

        $_SESSION['success'] = "Voter Deleted!";

    }catch(PDOException $e){
        $_SESSION['status'] = "Failed to delete Voter: " . $e->getMessage();
    }
    header('Location: voter_list.php');
    exit();
}
?>
