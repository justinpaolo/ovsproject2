<?php

ob_start(); // Start output buffering
include('security.php');
include('include/header.php');
include('include/navbar.php');
include('include/conn.php');

if (isset($_POST['approve_btn'])) {
    $candidate_id = $_POST['candidate_id'];

    try {
        $query = "UPDATE candidate SET status = 'approved' WHERE candidate_id = :candidate_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':candidate_id', $candidate_id);
        $stmt->execute();

        $_SESSION['success'] = "Candidate approved successfully.";
        header('Location: candidates_approval.php');
        exit();
    } catch (PDOException $e) {
        $_SESSION['status'] = "Error: " . $e->getMessage();
        header('Location: candidates_approval.php');
        exit();
    }
}

if (isset($_POST['reject_btn'])) {
    $candidate_id = $_POST['candidate_id'];

    try {
        $query = "UPDATE candidate SET status = 'rejected' WHERE candidate_id = :candidate_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':candidate_id', $candidate_id);
        $stmt->execute();

        $_SESSION['success'] = "Candidate rejected successfully.";
        header('Location: candidates_approval.php');
        exit();
    } catch (PDOException $e) {
        $_SESSION['status'] = "Error: " . $e->getMessage();
        header('Location: candidates_approval.php');
        exit();
    }
}
?>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Pending Candidate Approvals</h6>
        </div>
        <div class="card-body">
            <?php
            if(isset($_SESSION['success']) && $_SESSION['success'] != '') {
                echo '<div class="alert alert-success alert-dismissible fade show">
                    ' . $_SESSION['success'] . '
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>';
                unset($_SESSION['success']);
            }

            if(isset($_SESSION['status']) && $_SESSION['status'] != '') {
                echo '<div class="alert alert-danger alert-dismissible fade show">
                    ' . $_SESSION['status'] . '
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>';
                unset($_SESSION['status']);
            }
            ?>

            <div class="table-responsive">
                <?php
                try {
                    // Get all candidates with pending status
                    $query = "SELECT * FROM candidate WHERE status = 'pending' ORDER BY date_registered DESC";
                    $stmt = $conn->query($query);
                    ?>
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Election Type</th>
                                <th>Position</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Grade Level</th>
                                <th>Gender</th>
                                <th>Party</th>
                                <th>Image</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if($stmt->rowCount() > 0) {
                                while($row = $stmt->fetch()) {
                                    ?>
                                    <tr>
                                        <td><?php echo $row['candidate_id']; ?></td>
                                        <td><?php echo isset($row['election_type']) ? $row['election_type'] : 'N/A'; ?></td>
                                        <td><?php echo $row['position']; ?></td>
                                        <td><?php echo $row['firstname']; ?></td>
                                        <td><?php echo $row['lastname']; ?></td>
                                        <td><?php echo $row['grade_level']; ?></td>
                                        <td><?php echo $row['gender']; ?></td>
                                        <td><?php echo $row['party']; ?></td>
                                        <td>
                                            <?php if(!empty($row['img'])) { ?>
                                                <img src="uploads/<?php echo $row['img']; ?>" width="50" height="50" alt="Candidate Image">
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <?php 
                                            if($row['status'] == 'pending') {
                                                echo '<span class="badge badge-warning">Pending</span>';
                                            } elseif($row['status'] == 'approved') {
                                                echo '<span class="badge badge-success">Approved</span>';
                                            } else {
                                                echo '<span class="badge badge-danger">Rejected</span>';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php if($row['status'] == 'pending') { ?>
                                                <form action="candidates_approval.php" method="POST" class="d-inline">
                                                    <input type="hidden" name="candidate_id" value="<?php echo $row['candidate_id']; ?>">
                                                    <button type="submit" name="approve_btn" class="btn btn-success btn-sm">
                                                        <i class="fas fa-check"></i> Approve
                                                    </button>
                                                    <button type="submit" name="reject_btn" class="btn btn-danger btn-sm">
                                                        <i class="fas fa-times"></i> Reject
                                                    </button>
                                                </form>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                echo "<tr><td colspan='10' class='text-center'>No candidates found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                    <?php
                } catch(PDOException $e) {
                    echo "Error: " . $e->getMessage();
                }
                ?>
            </div>
        </div>
    </div>
</div>

<?php
include('include/scripts.php');

ob_end_flush(); // End output buffering and flush output
?>