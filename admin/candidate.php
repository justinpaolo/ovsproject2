<?php

include('security.php');
include('include/header.php');
include('include/navbar.php');

?>

<!-- Modal -->
<div class="modal fade" id="addcandidate" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fs-5" id="exampleModalLabel">Add Candidate</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      
      <form action="candidateprocess.php" method="POST" enctype="multipart/form-data">
        <div class="modal-body">
          <div class="form-group">
            <label>Election Type</label>
            <select id="election_type" class="form-control" name="election_type" required>
              <option value="">Select Election Type</option>
              <option value="National">National Election</option>
              <option value="Local">Local Election</option>
            </select>
          </div>
          <div class="form-group">
            <label>Position</label>
            <select id="position" class="form-control" name="position">
              <option>SELECT NATIONAL CANDIDATES POSITION</option>
              <option>PRESIDENT</option>
              <option>VP-INTERNAL</option>
              <option>VP-EXTERNAL</option>
              <option>SECRETARY</option>
              <option>TREASURER</option>
              <option>AUDITOR</option>
            </select>
          </div>

          <div class="form-group">
            <label>Firstname</label>
            <input type="text" name="fname" class="form-control" placeholder="Firstname">
          </div>

          <div class="form-group">
            <label>Lastname</label>
            <input type="text" name="lname" class="form-control" placeholder="Lastname">
          </div>

          <div class="form-group">
            <label>Year Level</label>
            <select id="gradelvl" name="grade_level" class="form-control">
              <option>1st Year</option>
              <option>2nd Year</option>
              <option>3rd Year</option>
              <option>4th Year</option>
            </select>
          </div>

          <div class="form-group">
            <label>Gender</label>
            <select class="form-control" name="gender">
                <option></option>
                <option>Male</option>
                <option>Female</option>
            </select>
          </div>
          <div class="form-group">
            <label for="txtbranch">Department</label>
            <select class="form-control" id="txtbranch" name="department" required>
              <option></option>
              <option>College of Management</option>
              <option>College of Accountancy</option>
              <option>College of Education</option>
              <option>College Of Criminal Justice Education</option>
              <option>College of Engineering</option>
              <option>College of Allied Health Sciences</option>
              <option>College of Information Technology</option>
              <option>College of Maritime Education</option>
            </select>
          </div>

          <div class="form-group">
            <label>Party</label>
            <select class="form-control" name="party">
                <option></option>
                <option>Kusog sang pamatan-on (KSP)</option>
                <option>Student Democratic Alliance (SDA)</option>
            </select>
          </div>

          <div class="form-group">
            <label>Image</label>
            <input type="file" name="photo" class="form-control">
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" name="candidatebtn" class="btn btn-primary">Add Candidate</button>
        </div>
      </form>
    </div>
  </div>
</div> 

<div class="container-fluid">

  <!-- DataTables Example -->
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Candidates</h6>
      
      <!-- Button trigger modal -->
      <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addcandidate">
        Add candidate
      </button>

      <!-- Department filter dropdown -->
      <form method="GET" style="display:inline-block; margin-left:16px;">
        <label for="filter_department" style="font-weight:600; color:#355c36; margin-right:6px;">Department:</label>
        <select name="filter_department" id="filter_department" onchange="this.form.submit()" style="padding:6px 12px; border-radius:6px;">
          <option value="">All Departments</option>
          <option value="College of Management" <?php if(isset($_GET['filter_department']) && $_GET['filter_department']=='College of Management') echo 'selected'; ?>>College of Management</option>
          <option value="College of Accountancy" <?php if(isset($_GET['filter_department']) && $_GET['filter_department']=='College of Accountancy') echo 'selected'; ?>>College of Accountancy</option>
          <option value="College of Education" <?php if(isset($_GET['filter_department']) && $_GET['filter_department']=='College of Education') echo 'selected'; ?>>College of Education</option>
          <option value="College Of Criminal Justice Education" <?php if(isset($_GET['filter_department']) && $_GET['filter_department']=='College Of Criminal Justice Education') echo 'selected'; ?>>College Of Criminal Justice Education</option>
          <option value="College of Engineering" <?php if(isset($_GET['filter_department']) && $_GET['filter_department']=='College of Engineering') echo 'selected'; ?>>College of Engineering</option>
          <option value="College of Allied Health Sciences" <?php if(isset($_GET['filter_department']) && $_GET['filter_department']=='College of Allied Health Sciences') echo 'selected'; ?>>College of Allied Health Sciences</option>
          <option value="College of Information Technology" <?php if(isset($_GET['filter_department']) && $_GET['filter_department']=='College of Information Technology') echo 'selected'; ?>>College of Information Technology</option>
          <option value="College of Maritime Education" <?php if(isset($_GET['filter_department']) && $_GET['filter_department']=='College of Maritime Education') echo 'selected'; ?>>College of Maritime Education</option>
        </select>
      </form>
    </div>

    <div class="card-body" >

    <?php
    if(isset($_SESSION['success']) && $_SESSION['success'] != ''){
        echo '<h2 class="bg-primary">' . $_SESSION['success'].'</h2>';
        unset($_SESSION['success']);

    }
        if(isset($_SESSION['status']) && $_SESSION['status'] != ''){
            echo '<h2 class="bg-danger">'.$_SESSION['status'].'</h2>';
            unset($_SESSION['status']);
    } 
    
    ?>

      <div class="table-responsive">
        <?php
        include("include/conn.php");
        $query = "SELECT * FROM candidate";
        if (isset($_GET['filter_department']) && $_GET['filter_department'] != '') {
          $query .= " WHERE department = '" . addslashes($_GET['filter_department']) . "'";
        }
        $query_run = $conn->query($query);  // Changed to PDO query
        ?>

        <table class="table table-bordered" id="datatable" width="100%" cellspacing="0">
          <thead>
            <tr>
              <th>ID</th>
              <th>Election Type</th>
              <th>Image</th>
              <th>Position</th>
              <th>Firstname</th>
              <th>Lastname</th>
              <th>Year Level</th>
              <th>Gender</th>
              <th>Party</th>
              <th>Department</th>
              <th>Edit</th>
              <th>Delete</th>
            </tr>
          </thead>
          <tbody>
            <?php
            if($query_run->rowCount() > 0) {  // Changed to PDO rowCount
                while($row = $query_run->fetch()) {  // Changed to PDO fetch
                    ?>
            <tr>
              <td><?php echo $row['candidate_id']; ?></td>
              <td><?php echo isset($row['election_type']) ? $row['election_type'] : 'N/A'; ?></td>
              <td><img src="uploads/<?php echo $row['img']; ?>" width="50" height="50" alt="Candidate Image"></td>
              <td><?php echo $row['position']; ?></td>
              <td><?php echo $row['firstname']; ?></td>
              <td><?php echo $row['lastname']; ?></td>
              <td><?php echo $row['grade_level']; ?></td>
              <td><?php echo $row['gender']; ?></td>
              <td><?php echo $row['party']; ?></td>
              <td><?php echo $row['department']; ?></td>

                    <form action="candidate_edit.php" method="post">
                        <input type="hidden" name="edit_id" value="<?php echo $row['candidate_id']; ?>">
              <td><button type="submit" name="edit_btn" class="btn btn-success btn-sm">EDIT</button></td>
              </form>


              <form action="candidate_delete.php" method="post">
                <input type="hidden" name="delete_id"  value="<?php echo $row['candidate_id'];?>">
              <td><button type="submit" name="deletebtn" class="btn btn-danger btn-sm">DELETE</button></td>
              </form>
            </tr>
            <?php
                }
            } else {
                echo "No record";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>

<?php

include('include/scripts.php');

?>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const electionEl = document.getElementById('election_type');
  const positionEl = document.getElementById('position');

  const positions = {
    'National': [
      'SELECT CANDIDATE POSITION',
      'PRESIDENT',
      'VP-INTERNAL',
      'VP-EXTERNAL',
      'SECRETARY',
      'TREASURER',
      'AUDITOR'
    ],
    'Local': [
      'SELECT CANDIDATE POSITION',
      'Chairman',
      'Vice Chairman',
      'Secretary',
      'Treasurer',
      'Auditor',
      'Board Member'
    ]
  };

  function populatePositions(type) {
    // Keep current selection if it exists in the new list
    const opts = positions[type] || ['SELECT CANDIDATE POSITION'];
    const current = positionEl.value;
    positionEl.innerHTML = '';
    opts.forEach(function (p) {
      const opt = document.createElement('option');
      opt.value = p;
      opt.textContent = p;
      positionEl.appendChild(opt);
    });
    // restore selection if present
    if (opts.indexOf(current) !== -1) {
      positionEl.value = current;
    }
  }

  electionEl.addEventListener('change', function () {
    populatePositions(this.value);
  });

  // initialize based on current value (useful when editing)
  if (electionEl.value) {
    populatePositions(electionEl.value);
  }
});
</script>
