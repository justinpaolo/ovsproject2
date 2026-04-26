<?php
require_once 'include/conn.php';

// Check if archive table exists
$tableCheck = $conn->query("SHOW TABLES LIKE 'votes_archive'");
if ($tableCheck->rowCount() == 0) {
	echo '<div style="margin:40px auto;max-width:600px;text-align:center;color:#c00;font-size:1.3rem;">No archived votes found.</div>';
	exit;
}

// Get distinct years from archived votes
$years = $conn->query("SELECT DISTINCT YEAR(vote_time) as year FROM votes_archive ORDER BY year DESC")->fetchAll();

// Get selected year from GET
$selectedYear = isset($_GET['year']) ? $_GET['year'] : '';

echo '<div style="max-width:900px;margin:40px auto;">';
echo '<h2 style="text-align:center;color:#355c36;font-weight:700;margin-bottom:32px;">Previous Year Election Results</h2>';

// Year selection dropdown
echo '<form method="GET" style="margin-bottom:24px;text-align:center;">';
echo '<label for="year" style="font-weight:600;color:#355c36;margin-right:10px;">Select Year:</label>';
echo '<select name="year" id="year" onchange="this.form.submit()" style="padding:6px 12px;border-radius:6px;">';
echo '<option value="">All Years</option>';
foreach ($years as $y) {
	$year = $y['year'];
	echo '<option value="' . $year . '"' . ($selectedYear == $year ? ' selected' : '') . '>' . $year . '</option>';
}
echo '</select>';
echo '</form>';

// Show results for selected year or all years
foreach ($years as $y) {
	$year = $y['year'];
	if ($selectedYear && $selectedYear != $year) continue;
	echo '<div style="background:#e6f0ea;border-radius:12px;padding:24px;margin-bottom:32px;box-shadow:0 4px 16px rgba(53,92,54,0.08);">';
	echo '<h3 style="color:#3c763d;font-weight:600;margin-bottom:18px;">Year: ' . $year . '</h3>';

	// Get archived votes for this year
	$sql = "SELECT c.position, c.firstname, c.lastname, c.department, c.party, COUNT(v.vote_id) as votes
			FROM votes_archive v
			JOIN candidate c ON v.candidate_id = c.candidate_id
			WHERE YEAR(v.vote_time) = ?
			GROUP BY c.candidate_id
			ORDER BY c.position, votes DESC";
	$stmt = $conn->prepare($sql);
	$stmt->execute([$year]);
	$results = $stmt->fetchAll();

	if (count($results) > 0) {
		echo '<table style="width:100%;margin-bottom:18px;background:#fff;border-radius:8px;box-shadow:0 2px 8px rgba(53,92,54,0.05);">';
		echo '<tr style="background:#c8d5b9;color:#355c36;font-weight:600;">';
		echo '<th style="padding:10px;">Position</th><th>First Name</th><th>Last Name</th><th>Department</th><th>Party</th><th>Votes</th>';
		echo '</tr>';
		foreach ($results as $row) {
			echo '<tr style="text-align:center;">';
			echo '<td style="padding:8px;">' . htmlspecialchars($row['position']) . '</td>';
			echo '<td>' . htmlspecialchars($row['firstname']) . '</td>';
			echo '<td>' . htmlspecialchars($row['lastname']) . '</td>';
			echo '<td>' . htmlspecialchars($row['department']) . '</td>';
			echo '<td>' . htmlspecialchars($row['party']) . '</td>';
			echo '<td>' . htmlspecialchars($row['votes']) . '</td>';
			echo '</tr>';
		}
		echo '</table>';
	} else {
		echo '<div style="color:#6b8e6b;text-align:center;">No archived votes for this year.</div>';
	}
	echo '</div>';
}
echo '</div>'; 
?>
