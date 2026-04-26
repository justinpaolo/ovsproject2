<?php
session_start();
include('../admin/include/conn.php');

if (!isset($_SESSION['voter_id'])) {
    header('Location: ../index.php');
    exit();
}
$voter_id = $_SESSION['voter_id'];

// Fetch voter details from DB
$stmt = $conn->prepare("SELECT firstname, lastname, date, validation_status, email FROM voters WHERE voter_id = ?");
$stmt->execute([$voter_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$picture = $_SESSION['picture'] ?? '';
$name = $_SESSION['name'] ?? ($user['firstname'] . ' ' . $user['lastname']);
$email = $_SESSION['email'] ?? ($user['email'] ?? '');
$joinDate = $user['date'] ?? '';
$status = $user['validation_status'] ?? 'pending';

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <title>Profile</title>
    <style>
        body { background: linear-gradient(135deg, #e2ecec, #c8d5b9); font-family: 'Poppins', sans-serif; }
        .profile-card { max-width:700px; margin:40px auto; background:#e6f0ea; padding:30px; border-radius:14px; box-shadow:0 8px 20px rgba(0,0,0,0.08); text-align:center; }
        .avatar { width:140px; height:140px; border-radius:50%; object-fit:cover; border:4px solid #fff; box-shadow:0 6px 14px rgba(0,0,0,0.08); }
        .name { font-size:22px; font-weight:700; color:#2f6f33; margin-top:14px; }
        .meta { color:#556b56; margin-top:8px; }
        .badge-status { padding:8px 12px; border-radius:10px; font-weight:700; }
        .badge-accepted { background:#6fc06f; color:white; }
        .badge-pending { background:#f0b27a; color:white; }
        .back-btn { margin-top:18px; }
    </style>
</head>
<body>
    <div class="profile-card">
        <?php if ($picture): ?>
            <img src="<?php echo htmlspecialchars($picture); ?>" alt="avatar" class="avatar">
        <?php else: ?>
            <div style="width:140px;height:140px;border-radius:50%;background:#cfe6d0;display:inline-block;
                        line-height:140px;font-size:36px;color:#fff;font-weight:700;"><?php echo strtoupper(substr($name,0,1)); ?></div>
        <?php endif; ?>

        <div class="name"><?php echo htmlspecialchars($name); ?></div>
        <div class="meta"><?php echo htmlspecialchars($email); ?></div>
        <div class="meta">Joined: <?php echo htmlspecialchars($joinDate); ?></div>
        <div style="margin-top:12px;">
            <?php if (strtolower($status) === 'accepted'): ?>
                <span class="badge-status badge-accepted">Approved</span>
            <?php else: ?>
                <span class="badge-status badge-pending">Pending Approval</span>
            <?php endif; ?>
        </div>

        <div class="back-btn">
            <a href="index.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>