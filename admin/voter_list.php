<?php
session_start();

include('security.php');
include('include/header.php');
include('include/navbar.php');
?>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Voters List</h6>        
        </div>
        <div class="card-body">
            <?php
            if(isset($_SESSION['success']) && $_SESSION['success'] != ''){
                echo '<h2 class="bg-primary text-white">' . $_SESSION['success'] . '</h2>';
                unset($_SESSION['success']);
            }
            if(isset($_SESSION['status']) && $_SESSION['status'] != ''){
                echo '<h2 class="bg-primary text-white">' . $_SESSION['status'] . '</h2>';
                unset($_SESSION['status']);
            }
            ?>

            <div class="table-responsive">
                <?php
                    include('include/conn.php');
                    try {
                        $query = "SELECT * FROM voters WHERE account = 'Voter'";
                        $stmt = $conn->prepare($query);
                        $stmt->execute();
                    
                ?>
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Voter ID</th>
                            <th>ID Number</th>
                            <th>Fullname</th>
                            <!-- <th>Gender</th> -->
                            <th>Program</th>
                            <th>Year Level</th>
                            <th>Status</th>
                            <th>Account</th>
                            <th>Date</th>
                            <!-- Delete column removed -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if($stmt->rowCount() > 0){
                            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){                     
                             ?>
                        <tr>
                            <td><?php echo $row['voter_id']; ?></td>
                            <td><?php echo $row['id_number']; ?></td>
                            <?php
                                // prefer the combined name column if present, otherwise concat firstname + lastname
                                $displayName = '';
                                if (!empty($row['name'])) {
                                    $displayName = $row['name'];
                                } else {
                                    $displayName = trim(($row['firstname'] ?? '') . ' ' . ($row['lastname'] ?? ''));
                                }
                            ?>
                            <td><?php echo htmlspecialchars($displayName); ?></td>
                            <!-- <td><?php echo $row['gender']; ?></td> -->
                            <td><?php echo $row['program']; ?></td>
                            <td>
                                <?php
                                $yl = isset($row['year_level']) ? trim($row['year_level']) : '';
                                // if stored as numeric (1-5) convert to ordinal year text
                                if ($yl === '1' || $yl === '1st' || stripos($yl,'1') === 0) { echo '1st Year'; }
                                elseif ($yl === '2' || $yl === '2nd' || stripos($yl,'2') === 0) { echo '2nd Year'; }
                                elseif ($yl === '3' || $yl === '3rd' || stripos($yl,'3') === 0) { echo '3rd Year'; }
                                elseif ($yl === '4' || $yl === '4th' || stripos($yl,'4') === 0) { echo '4th Year'; }
                                elseif ($yl === '5' || $yl === '5th' || stripos($yl,'5') === 0) { echo '5th Year'; }
                                else { echo (!empty($yl) ? htmlspecialchars($yl) : 'N/A'); }
                                ?>
                            </td>
                            <td>
                                <?php
                                if($row['status'] == 0){
                                    echo '<span class="badge badge-warning">Not Voted</span>';
                                }else{
                                    echo '<span class="badge badge-success">Voted</span>';
                                }
                                ?>
                            </td>
                            <td><?php echo $row['account']; ?></td>
                            <td><?php echo $row['date']; ?></td>
                            <!-- delete cell removed -->
                        </tr>
                        <?php
                            }
                        }else{
                            echo "<tr><td colspan='8' class='text-center'>No voters</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
                <?php
                }catch(PDOException $e){
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