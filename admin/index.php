<?php



include('security.php');
include('include/header.php');
include('include/navbar.php');
include('include/conn.php');


?>

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column" style="background-color: #0080FF;">

<!-- Main Content -->
<div id="content">

    <!-- Topbar -->
    <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

        <!-- Sidebar Toggle (Topbar) -->
        <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
            <i class="fa fa-bars"></i>
        </button>

        <!-- PHINMA LOGO -->
        <!-- <div class="mx-auto" style="width:260px;display:flex;justify-content:center;align-items:center;">
            <img src="img/PHINMA-Ed-Logo.png" alt="PHINMA Education Logo" style="height:70px;max-width:220px;object-fit:contain;">
        </div> -->

        <!-- Topbar Navbar -->
        <ul class="navbar-nav ml-auto">

            <!-- Nav Item - Search Dropdown (Visible Only XS) -->
            <li class="nav-item dropdown no-arrow d-sm-none">
                <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-search fa-fw"></i>
                </a>
                <!-- Dropdown - Messages -->
                <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                    aria-labelledby="searchDropdown">
                    <form class="form-inline mr-auto w-100 navbar-search">
                        <div class="input-group">
                            <input type="text" class="form-control bg-light border-0 small"
                                placeholder="Search for..." aria-label="Search"
                                aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </li>


            <div class="topbar-divider d-none d-sm-block"></div>

            <!-- Nav Item - User Information -->
            <li class="nav-item dropdown no-arrow">
                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                    <?php 
                        if(isset($_SESSION['username'])) {
                            echo $_SESSION['admin_name']; 
                        } else {
                            echo "Administrator";
                        }
                        ?>
                    </span>
                    <img class="img-profile rounded-circle"
                        src="img/undraw_profile.svg">
                </a>
                <!-- Dropdown - User Information -->
                <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                    aria-labelledby="userDropdown">
                    <a class="dropdown-item" href="#">
                        <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                        Profile
                    </a>
                    <a class="dropdown-item" href="#">
                        <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                        Settings
                    </a>
                    <a class="dropdown-item" href="#">
                        <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                        Activity Log
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="adminlogin.php" data-toggle="modal" data-target="#logoutModal">
                        <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                        Logout
                    </a>
                </div>
            </li>

        </ul>

    </nav>
    <!-- End of Topbar -->

    <!-- Begin Page Content -->
    <div class="container-fluid">

        

        <!-- Page Heading
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
            <a href="index.php" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                    class="fas fa-download fa-sm text-white-50"></i> Election Report</a>
        </div> -->

        <!-- Content Row -->
        <div class="row">

            <!-- Earnings (Monthly) Card Example -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Cadidates</div>

                                    <?php
                            
                            $query = "SELECT count(*) as total from candidate";
                           $stmt = $conn->query($query);
                           $result = $stmt->fetch(PDO::FETCH_ASSOC);
                           echo '<h4> Total candidate : '.$result['total'].'</h4>';

                

                        ?>

                                <div class="h5 mb-0 font-weight-bold text-gray-800"></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-user fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Earnings (Monthly) Card Example -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    President</div>

                                    <?php
                            
                            $query = "SELECT count(*) as total from candidate where position = 'President'";
                           $stmt = $conn->query($query);
                           $result = $stmt->fetch(PDO::FETCH_ASSOC);
                           echo '<h4> Total President : '.$result['total'].'</h4>';

                

                        ?>

                                <div class="h5 mb-0 font-weight-bold text-gray-800"></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-user fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Earnings (Monthly) Card Example -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">VP-INTERNAL
                                </div>

                                <?php
                            
                            $query = "SELECT count(*) as total from candidate where position = 'VP-INTERNAL'";
                           $stmt = $conn->query($query);
                           $result = $stmt->fetch(PDO::FETCH_ASSOC);
                           echo '<h4> Total VP-Internal : '.$result['total'].'</h4>';

                

                        ?>

                                <div class="row no-gutters align-items-center">
                                    <div class="col-auto">
                                        <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800"></div>
                                    </div>
                                    <div class="col">
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-user fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Requests Card Example -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    VP-EXTERNAL</div>

                                    <?php
                            
                            $query = "SELECT count(*) as total from candidate where position = 'VP-EXTERNAL'";
                           $stmt = $conn->query($query);
                           $result = $stmt->fetch(PDO::FETCH_ASSOC);
                           echo '<h4> Total VP-External : '.$result['total'].'</h4>';

                

                        ?>

                                <div class="h5 mb-0 font-weight-bold text-gray-800"></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-user fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- End of dashboard cards row -->
        <!-- Department Filter -->
        <form method="GET" class="mb-4">
            <div class="form-row align-items-center">
                <div class="col-auto">
                    <label class="sr-only" for="departmentSelect">Department</label>
                    <select class="form-control mb-2" id="departmentSelect" name="department">
                        <option value="">All Departments</option>
                        <?php
                        // Fetch unique departments from the candidate table
                        $deptQuery = "SELECT DISTINCT department FROM candidate";
                        $deptStmt = $conn->query($deptQuery);
                        while ($dept = $deptStmt->fetch(PDO::FETCH_ASSOC)) {
                            $selected = (isset($_GET['department']) && $_GET['department'] == $dept['department']) ? 'selected' : '';
                            echo '<option value="' . htmlspecialchars($dept['department']) . '" ' . $selected . '>' . htmlspecialchars($dept['department']) . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary mb-2">Filter</button>
                </div>
            </div>
        </form>
        <!-- End Department Filter -->

        <!-- Candidates Table -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Candidates List</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Photo</th>
                                <th>Firstame</th>
                                <th>Lastname</th>
                                <th>Department</th>
                                <th>Position</th>
                                <th>Year Level</th>
                                <!-- Add more columns if needed -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Prepare filter
                            $params = [];
                            $where = '';
                            if (!empty($_GET['department'])) {
                                $where = "WHERE department = :department";
                                $params[':department'] = $_GET['department'];
                            }
                            $query = "SELECT * FROM candidate $where ORDER BY department, position, firstname, lastname";
                            $stmt = $conn->prepare($query);
                            $stmt->execute($params);
                            $i = 1;
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo "<tr>";
                                echo "<td>" . $i++ . "</td>";
                                // Candidate photo
                                if (!empty($row['img'])) {
                                    echo "<td><img src='uploads/" . htmlspecialchars($row['img']) . "' alt='Photo' width='50' height='50' style='object-fit:cover; border-radius:50%;'></td>";
                                } else {
                                    echo "<td><img src='uploads/default-profile.png' alt='No Photo' width='50' height='50' style='object-fit:cover; border-radius:50%;'></td>";
                                }
                                echo "<td>" . htmlspecialchars($row['firstname']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['lastname']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['department']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['position']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['grade_level']) . "</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- End Candidates Table -->

    </div>
    <!-- /.container-fluid -->

</div>
<!-- End of Main Content -->


</div>
<!-- End of Content Wrapper -->

</div>
<!-- End of Page Wrapper -->

<?php
include('include/scripts.php');
include('include/footer.php');
?>