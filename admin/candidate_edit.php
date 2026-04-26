<?php

include('include/header.php');
include('include/navbar.php');
?>

<div class="container-fluid">
  <!-- DataTables Example -->
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Edit Candidates</h6>
    </div>

    <div class="card-body">
      <?php
      //This code is for Editing / Retrieving Data
      $conn = mysqli_connect("localhost", "root", "", "ovsproject");

      if (isset($_POST['edit_btn']) && isset($_POST['edit_id'])) {
        $candidate_id = $_POST['edit_id'];
        $query = "SELECT * FROM candidate WHERE candidate_id='$candidate_id'";
        $query_run = mysqli_query($conn, $query);

        foreach ($query_run as $row) {
      ?>
          <form action="candidateprocess.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="edit_id" value="<?php echo $row['candidate_id']; ?>">
            <div class="form-group">
              <label>Election Type</label>
              <select class="form-control" name="edit_election_type" required>
                <option value="National" <?php if(isset($row['election_type']) && $row['election_type'] == 'National') echo 'selected'; ?>>National Election</option>
                <option value="Local" <?php if(isset($row['election_type']) && $row['election_type'] == 'Local') echo 'selected'; ?>>Local Election</option>
              </select>
            </div>
            <div class="form-group">
              <label>Position</label>
              <select class="form-control" name="edit_position">
                <option <?php if ($row['position'] == 'PRESIDENT') echo 'selected'; ?>>PRESIDENT</option>
                <option <?php if ($row['position'] == 'VP-INTERNAL') echo 'selected'; ?>>VP-INTERNAL</option>
                <option <?php if ($row['position'] == 'VP-EXTERNAL') echo 'selected'; ?>>VP-EXTERNAL</option>
                <option <?php if ($row['position'] == 'SECRETARY') echo 'selected'; ?>>SECRETARY</option>
                <option <?php if ($row['position'] == 'TREASURER') echo 'selected'; ?>>TREASURER</option>
                <option <?php if ($row['position'] == 'AUDITOR') echo 'selected'; ?>>AUDITOR</option>
              </select>
            </div>

            <div class="form-group">
              <label>Firstname</label>
              <input type="text" name="edit_fname" value="<?php echo $row['firstname']; ?>" class="form-control" placeholder="Firstname">
            </div>

            <div class="form-group">
              <label>Lastname</label>
              <input type="text" name="edit_lname" value="<?php echo $row['lastname']; ?>" class="form-control" placeholder="Lastname">
            </div>

            <div class="form-group">
              <label>Year Level</label>
              <select id="gradelvl" name="edit_grade_level" class="form-control">
                <option <?php if ($row['grade_level'] == '1st Year') echo 'selected'; ?>>1st Year</option>
                <option <?php if ($row['grade_level'] == '2nd Year') echo 'selected'; ?>>2nd Year</option>
                <option <?php if ($row['grade_level'] == '3rd Year') echo 'selected'; ?>>3rd Year</option>
                <option <?php if ($row['grade_level'] == '4th Year') echo 'selected'; ?>>4th Year</option>
              </select>
            </div>

            <div class="form-group">
              <label>Department</label>
              <select id="department" name="edit_department" class="form-control">
                <option <?php if ($row['department'] == 'College of Management') echo 'selected'; ?>>College of Management</option>
                <option <?php if ($row['department'] == 'College of Accountancy') echo 'selected'; ?>>College of Accountancy</option>
                <option <?php if ($row['department'] == 'College of Education') echo 'selected'; ?>>College of Education</option>
                <option <?php if ($row['department'] == 'College Of Criminal Justice Education') echo 'selected'; ?>>College Of Criminal Justice Education</option>
                <option <?php if ($row['department'] == 'College of Engineering') echo 'selected'; ?>>College of Engineering</option>
                <option <?php if ($row['department'] == 'College of Allied Health Sciences') echo 'selected'; ?>>College of Allied Health Sciences</option>
                <option <?php if ($row['department'] == 'College of Information Technology') echo 'selected'; ?>>College of Information Technology</option>
                <option <?php if ($row['department'] == 'College of Maritime Education') echo 'selected'; ?>>College of Maritime Education</option>
              </select>
            </div>

            <div class="form-group">
              <label>Gender</label>
              <select class="form-control" name="edit_gender">
                <option <?php if ($row['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                <option <?php if ($row['gender'] == 'Female') echo 'selected'; ?>>Female</option>
              </select>
            </div>

            <div class="form-group">
              <label>Party</label>
              <select name="edit_party" class="form-control">
                <option <?php if ($row['party'] == 'KSP') echo 'selected'; ?>>Kusog sang Pamatan-on (KSP)</option>
                <option <?php if ($row['party'] == 'SDA') echo 'selected'; ?>>Student Democratic Alliance (SDA)</option>
              </select>
            </div>

            <div class="form-group">
              <label>Image</label>
              <img src="uploads/<?php echo $row['img']; ?>" width="100">
              <input type="file" name="edit_photo" class="form-control">
            </div>
            <a href="candidate.php" class="btn btn-danger">Cancel</a>
            <button type="submit" name="updatebtn" class="btn btn-primary">Update</button>
          </form>
      <?php
        }
      }
      ?>
    </div>
  </div>
</div>

<?php
include('include/footer.php');
include('include/scripts.php');
?>