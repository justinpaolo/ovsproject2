<!-- filepath: c:\xampp\htdocs\ovsproject2\dashboard\check_voting_status.php -->
<?php
include("../admin/include/conn.php");

try {
    $query = "SELECT voting_status FROM settings WHERE id = 1";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $voting_status = $stmt->fetchColumn();

    echo $voting_status; // Return the voting status (0 or 1)
} catch (PDOException $e) {
    echo "0"; // Default to voting disabled in case of an error
}
?>