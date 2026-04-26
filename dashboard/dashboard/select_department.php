<?php
session_start();
include('../admin/include/conn.php');

if (!isset($_SESSION['voter_id'])) {
    header('Location: ../index.php');
    exit();
}

$voter_id = $_SESSION['voter_id'];

// Get available departments from candidate table (Local only)
$deptQuery = "SELECT DISTINCT department FROM candidate WHERE election_type = 'Local' AND status = 'approved' ORDER BY department";
$deptStmt = $conn->query($deptQuery);
$departments = $deptStmt->fetchAll(PDO::FETCH_COLUMN);

// Always include these department options
$defaultDepartments = [
    'College of Management',
    'College of Accountancy',
    'College of Education',
    'College Of Criminal Justice Education',
    'College of Engineering',
    'College of Allied Health Sciences',
    'College of Information Technology',
    'College of Maritime Education'
];

$departments = array_unique(array_merge($defaultDepartments, $departments));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dept = isset($_POST['department']) ? trim($_POST['department']) : '';
    $id_number = isset($_POST['id_number']) ? trim($_POST['id_number']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $firstname = isset($_POST['firstname']) ? trim($_POST['firstname']) : '';
    $lastname = isset($_POST['lastname']) ? trim($_POST['lastname']) : '';
    $full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
    $year_level = isset($_POST['year_level']) ? trim($_POST['year_level']) : '';

    if ($dept) {
        // Determine full name: prefer submitted full_name, else infer from email local-part or session
        if (empty($full_name)) {
            if (!empty($email)) {
                $local = strtolower(strtok($email, '@'));
                $local = str_replace(array('.', '_', '-', '+'), ' ', $local);
                $parts = preg_split('/\s+/', $local, -1, PREG_SPLIT_NO_EMPTY);
                $full_name = !empty($parts) ? ucfirst($parts[0]) . (count($parts) > 1 ? ' ' . implode(' ', array_map('ucfirst', array_slice($parts,1))) : '') : '';
            } elseif (!empty($_SESSION['name'])) {
                $full_name = trim($_SESSION['name']);
            }
        }

        // Split full_name into firstname/lastname
        $firstname = '';
        $lastname = '';
        if (!empty($full_name)) {
            $p = preg_split('/\s+/', trim($full_name));
            $firstname = $p[0] ?? '';
            $lastname = count($p) > 1 ? implode(' ', array_slice($p, 1)) : '';
        }

        // update voters.program and personal info
        try {
            // detect if voters table has email column
            $hasEmailCol = false;
            try {
                $colCheck = $conn->query("SHOW COLUMNS FROM voters LIKE 'email'")->fetch();
                if ($colCheck) $hasEmailCol = true;
            } catch (Exception $e) {
                $hasEmailCol = false;
            }

            if ($hasEmailCol) {
                $upd = $conn->prepare("UPDATE voters SET program = ?, id_number = ?, firstname = ?, lastname = ?, name = ?, email = ?, year_level = ? WHERE voter_id = ?");
                $upd->execute([$dept, $id_number, $firstname, $lastname, $full_name, $email, $year_level, $voter_id]);
            } else {
                $upd = $conn->prepare("UPDATE voters SET program = ?, id_number = ?, firstname = ?, lastname = ?, name = ?, year_level = ? WHERE voter_id = ?");
                $upd->execute([$dept, $id_number, $firstname, $lastname, $full_name, $year_level, $voter_id]);
            }

            // update session values
            $_SESSION['department'] = $dept;
            if (!empty($email)) $_SESSION['email'] = $email;
            if (!empty($firstname) || !empty($lastname)) $_SESSION['name'] = trim($firstname . ' ' . $lastname);

            // After selecting a department, send the user to the dashboard so they follow the
            // National -> Local voting flow (do not go directly to Local voting page).
            header('Location: index.php');
            exit();
        } catch (Exception $e) {
            $error = 'Could not save department or personal info. Please try again.';
        }
    } else {
        $error = 'Please select a department.';
    }
}

?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Select Department</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        body{background:linear-gradient(135deg,#e2ecec,#c8d5b9);font-family:Arial,sans-serif}
        .box{max-width:600px;margin:60px auto;background:#e6f0ea;padding:28px;border-radius:12px}
        .btn-primary{background:#355c36;border-color:#355c36}
    </style>
</head>
<body>
<div class="box">
    <h3>Fill Student Information (must be accurate)</h3>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <form method="post" id="selectDeptForm">
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="department">Department</label>
                <select name="department" id="department" class="form-control" required>
                    <option value="">-- Select Department --</option>
                    <?php foreach ($departments as $d): ?>
                        <option value="<?php echo htmlspecialchars($d); ?>" <?php if (isset($_SESSION['department']) && $_SESSION['department']==$d) echo 'selected'; ?>><?php echo htmlspecialchars($d); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="id_number">ID Number</label>
                <input type="text" name="id_number" id="id_number" class="form-control" value="<?php echo isset($_SESSION['id_number']) ? htmlspecialchars($_SESSION['id_number']) : ''; ?>" placeholder="Student ID" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control" value="<?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : ''; ?>" placeholder="you@phinmaed.com" required>
            </div>
            <div class="form-group col-md-4">
                <label for="full_name">Full name</label>
                <input type="text" name="full_name" id="full_name" class="form-control" value="<?php echo isset($_SESSION['name']) ? htmlspecialchars($_SESSION['name']) : ''; ?>">
                <small class="form-text text-muted">Auto-filled from your Google name or email; edit if incorrect.</small>
            </div>
            <div class="form-group col-md-2">
                <label for="year_level">Year</label>
                <select name="year_level" id="year_level" class="form-control" required>
                    <option value="">-- select year --</option>
                    <option value="1" <?php if(isset($_SESSION['year_level']) && $_SESSION['year_level']=='1') echo 'selected'; ?>>1</option>
                    <option value="2" <?php if(isset($_SESSION['year_level']) && $_SESSION['year_level']=='2') echo 'selected'; ?>>2</option>
                    <option value="3" <?php if(isset($_SESSION['year_level']) && $_SESSION['year_level']=='3') echo 'selected'; ?>>3</option>
                    <option value="4" <?php if(isset($_SESSION['year_level']) && $_SESSION['year_level']=='4') echo 'selected'; ?>>4</option>
                    <option value="5" <?php if(isset($_SESSION['year_level']) && $_SESSION['year_level']=='5') echo 'selected'; ?>>5</option>
                </select>
            </div>
        </div>

        <button class="btn btn-primary mt-3">Save and Continue</button>
    </form>

    <script>
        (function(){
            function parseEmailName(email) {
                if (!email) return {first:'', last:''};
                var local = email.split('@')[0];
                local = local.replace(/[._\-+]+/g,' ');
                var parts = local.trim().split(/\s+/);
                var first = parts.length ? parts[0] : '';
                var last = parts.length>1 ? parts.slice(1).join(' ') : '';
                // Capitalize
                first = first.charAt(0).toUpperCase() + first.slice(1);
                last = last.split(' ').map(function(p){ return p.charAt(0).toUpperCase()+p.slice(1); }).join(' ');
                return {first:first, last:last};
            }

            var emailEl = document.getElementById('email');
            var fullEl = document.getElementById('full_name');
            var userEdited = false;

            // If user edits the full name field, don't overwrite it from email updates
            fullEl.addEventListener('input', function(){ userEdited = true; });

            function updateFullFromEmail(){
                if (userEdited) return; // user already changed the full name
                var val = emailEl.value || '';
                var parts = parseEmailName(val);
                var full = (parts.first + (parts.last ? ' ' + parts.last : '')).trim();
                // Only set if the field is empty or equals the previous auto value
                if (!fullEl.value || fullEl.value === '') {
                    fullEl.value = full;
                }
            }

            emailEl.addEventListener('input', function(){
                updateFullFromEmail();
            });

            // run on load (only populate if empty)
            if (!fullEl.value) updateFullFromEmail();
        })();
    </script>
</div>
</body>
</html>
