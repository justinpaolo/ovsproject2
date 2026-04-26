<?php
session_start();
include('admin/include/conn.php'); // This should create a $conn PDO object

if (isset($_POST['credential'])) {
    $id_token = $_POST['credential'];
    $client_id = '463011006005-c0qi12rirk8ge31pul5egpnok83tdd3k.apps.googleusercontent.com';

    $url = "https://oauth2.googleapis.com/tokeninfo?id_token=" . $id_token;
    $response = file_get_contents($url);
    $data = json_decode($response, true);

    if ($data && isset($data['email']) && $data['aud'] === $client_id) {
    $email = $data['email'];
    $name = isset($data['name']) ? $data['name'] : '';
    $hd = isset($data['hd']) ? $data['hd'] : null; // hosted domain claim

        // ===== Temporary debug logging (remove after debugging) =====
        $debug = [];
        $debug['time'] = date('c');
        $debug['aud'] = isset($data['aud']) ? $data['aud'] : null;
        $debug['email'] = $email;
        $debug['hd'] = $hd;
    $debug['allowed_domains'] = ['students.phinmaed.com', 'phinmaed.com'];
        // write debug (append) to uploads/google_login_debug.log - no tokens saved
        $logPath = __DIR__ . '/uploads/google_login_debug.log';
        @file_put_contents($logPath, json_encode($debug) . PHP_EOL, FILE_APPEND | LOCK_EX);
        // ============================================================

        // Allowed domain(s)
    $allowed_domains = ['phinma.edu', 'phinma.edu.ph', 'phinmaed.com', 'students.phinmaed.com']; // replace with your campus domain(s)

    // Domains to auto-accept (bypass admin validation) — set your primary campus domains here
    $auto_accept_domains = ['phinmaed.com', 'phinma.edu', 'phinma.edu.ph', 'students.phinmaed.com'];

        // Robust domain check: allow exact match or any subdomain of an allowed domain
        $email_domain = strtolower(substr(strrchr($email, '@'), 1));
        $is_allowed = false;
        foreach ($allowed_domains as $d) {
            $d = strtolower($d);
            if ($email_domain === $d) {
                $is_allowed = true;
                break;
            }
            // allow subdomains (e.g. students.phinma.edu when allowed is phinma.edu)
            if (substr($email_domain, -strlen($d) - 1) === '.' . $d) {
                $is_allowed = true;
                break;
            }
        }

        // check if this domain should be auto-accepted
        $is_auto_accept = false;
        foreach ($auto_accept_domains as $d) {
            $d = strtolower($d);
            if ($email_domain === $d || substr($email_domain, -strlen($d) - 1) === '.' . $d) {
                $is_auto_accept = true;
                break;
            }
        }

        if (!$is_allowed) {
            // Not allowed — include returned domains in the status to help debugging
            $debugMsg = 'Only campus email addresses are allowed to sign in. ';
            // $debugMsg .= 'Returned email domain: ' . htmlspecialchars($email_domain) . '. ';
            // $debugMsg .= 'hd claim: ' . htmlspecialchars($hd);
            $_SESSION['status'] = $debugMsg;
            header('Location: index.php');
            exit();
        }

        // Split full name into firstname and lastname (best-effort)
        $name_parts = preg_split('/\s+/', trim($name));
        $firstname = $name_parts[0] ?? '';
        $lastname = isset($name_parts[1]) ? implode(' ', array_slice($name_parts, 1)) : '';

        // Detect if the voters table contains an 'email' column
        $hasEmailCol = false;
        try {
            $colCheck = $conn->query("SHOW COLUMNS FROM voters LIKE 'email'")->fetch();
            if ($colCheck) $hasEmailCol = true;
        } catch (Exception $e) {
            // ignore - assume no email column
            $hasEmailCol = false;
        }

        // Lookup existing user by email if possible, otherwise by firstname+lastname
        if ($hasEmailCol) {
            $stmt = $conn->prepare("SELECT voter_id, validation_status FROM voters WHERE email = ?");
            $stmt->execute([$email]);
        } else {
            $stmt = $conn->prepare("SELECT voter_id, validation_status FROM voters WHERE firstname = ? AND lastname = ?");
            $stmt->execute([$firstname, $lastname]);
        }
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            // If user exists but is not approved, either auto-accept or block
            $validation = isset($row['validation_status']) ? $row['validation_status'] : '';
            if (strtolower($validation) !== 'accepted') {
                if ($is_auto_accept) {
                    // auto-accept this user and allow login
                    try {
                        $upd = $conn->prepare("UPDATE voters SET validation_status = 'accepted' WHERE voter_id = ?");
                        $upd->execute([$row['voter_id']]);
                    } catch (Exception $e) {
                        // ignore update error and continue to block if necessary
                    }
                } else {
                    $_SESSION['status'] = 'Your account is awaiting admin approval before you can vote.';
                    header('Location: index.php');
                    exit();
                }
            }

            // Approved user — log them in, but require department selection if program empty
            $_SESSION['voter_id'] = $row['voter_id'];
            $_SESSION['email'] = $email;
            $_SESSION['name'] = $name;
            // store profile picture URL from Google if present
            if (isset($data['picture']) && !empty($data['picture'])) {
                $_SESSION['picture'] = $data['picture'];
            }
            try {
                $progStmt = $conn->prepare("SELECT program FROM voters WHERE voter_id = ?");
                $progStmt->execute([$row['voter_id']]);
                $progRow = $progStmt->fetch(PDO::FETCH_ASSOC);
                $program = $progRow['program'] ?? '';
                if (!empty($program)) {
                    $_SESSION['department'] = $program;
                    header('Location: dashboard/index.php');
                    exit();
                } else {
                    // require department selection
                    header('Location: dashboard/select_department.php');
                    exit();
                }
            } catch (Exception $e) {
                // fallback to dashboard on error
                header('Location: dashboard/index.php');
                exit();
            }
        } else {
            // Insert new voter record with required columns. If domain is auto-accepted, mark as 'accepted'
            $today = date('Y-m-d');
            $newValidationStatus = $is_auto_accept ? 'accepted' : 'pending';

            // Desired columns and values we want to insert (some may not exist in older schemas)
            $desired = [
                'id_number' => '',
                'firstname' => $firstname,
                'lastname' => $lastname,
                'gender' => '',
                'program' => '',
                'year_level' => '',
                'status' => 0,
                'account' => 'Voter',
                'date' => $today,
                'password' => '',
                'role' => 'voter',
                'validation_status' => $newValidationStatus,
                'name' => $name,
                'email' => $email
            ];

            // Find which columns are actually available in the voters table
            $availableCols = [];
            try {
                $cols = $conn->query("SHOW COLUMNS FROM voters")->fetchAll(PDO::FETCH_COLUMN);
                if ($cols) $availableCols = $cols;
            } catch (Exception $e) {
                // if SHOW COLUMNS fails, fallback to the keys we have
                $availableCols = array_keys($desired);
            }

            $insertCols = array_values(array_intersect($availableCols, array_keys($desired)));
            if (empty($insertCols)) {
                throw new Exception('No matching voter columns available for insert.');
            }

            $placeholders = implode(',', array_fill(0, count($insertCols), '?'));
            $insertQuery = 'INSERT INTO voters (' . implode(',', $insertCols) . ') VALUES (' . $placeholders . ')';
            $stmt_insert = $conn->prepare($insertQuery);
            $values = [];
            foreach ($insertCols as $c) {
                $values[] = $desired[$c] ?? null;
            }
            $stmt_insert->execute($values);
            $newId = $conn->lastInsertId();

            // If auto-accepted, log the user in immediately; otherwise notify awaiting approval
            if ($is_auto_accept) {
                // set session and redirect to department selection or dashboard based on program
                $_SESSION['voter_id'] = $newId;
                $_SESSION['email'] = $email;
                $_SESSION['name'] = $name;
                if (isset($data['picture']) && !empty($data['picture'])) {
                    $_SESSION['picture'] = $data['picture'];
                }
                try {
                    $progStmt = $conn->prepare("SELECT program FROM voters WHERE voter_id = ?");
                    $progStmt->execute([$newId]);
                    $progRow = $progStmt->fetch(PDO::FETCH_ASSOC);
                    $program = $progRow['program'] ?? '';
                    if (!empty($program)) {
                        $_SESSION['department'] = $program;
                        header('Location: dashboard/index.php');
                        exit();
                    } else {
                        header('Location: dashboard/select_department.php');
                        exit();
                    }
                } catch (Exception $e) {
                    header('Location: dashboard/index.php');
                    exit();
                }
            } else {
                $_SESSION['status'] = 'Your account has been created and is awaiting admin approval.';
                header('Location: index.php');
                exit();
            }
        }
    } else {
        $_SESSION['status'] = 'Google login failed.';
        header('Location: index.php');
        exit();
    }
} else {
    $_SESSION['status'] = 'No Google credential received.';
    header('Location: index.php');
    exit();
}
?>