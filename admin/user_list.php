<?php
session_start();

include('security.php');
include('include/header.php');
include('include/navbar.php');
?>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Students List</h6>
        </div>
        <div class="card-body">
            <?php
            if(isset($_SESSION['success']) && $_SESSION['success'] != '') {
                echo '<h2 class="bg-primary text-white">' . $_SESSION['success'] . '</h2>';
                unset($_SESSION['success']);
            }

            if(isset($_SESSION['status']) && $_SESSION['status'] != '') {
                echo '<h2 class="bg-danger text-white">' . $_SESSION['status'] . '</h2>';
                unset($_SESSION['status']);
            }
            ?>

            <div class="table-responsive">
                <?php
                include("include/conn.php");
                try {
                    $query = "SELECT * FROM voters WHERE account = 'User'";  // Only show users
                    $stmt = $conn->query($query);
                    ?>
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>ID Number</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Gender</th>
                                <th>Program</th>
                                <th>Year Level</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if($stmt->rowCount() > 0) {
                                while($row = $stmt->fetch()) {
                                    ?>
                                    <tr>
                                        <td><?php echo $row['voter_id']; ?></td>
                                        <td><?php echo $row['id_number']; ?></td>
                                        <td><?php echo $row['firstname']; ?></td>
                                        <td><?php echo $row['lastname']; ?></td>
                                        <td><?php echo $row['gender']; ?></td>
                                        <td><?php echo $row['program']; ?></td>
                                        <td><?php echo $row['year_level']; ?></td>
                                        <td>
                                            <form action="user_delete.php" method="POST">
                                                <input type="hidden" name="delete_id" value="<?php echo $row['voter_id']; ?>">
                                                <button type="submit" name="delete_btn" class="btn btn-danger btn-sm">DELETE</button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                echo "<tr><td colspan='8' class='text-center'>No Users Found</td></tr>";
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

?>