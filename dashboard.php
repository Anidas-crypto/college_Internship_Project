<?php
session_start();
if (!isset($_SESSION["admin"])) {
    header("Location: admin-login.php");
    exit;
}

include "config.php";
$error = "";
$student_info = "";
$results_table = "";

if (isset($_POST["search"])) {
    $roll = $_POST["roll"];

    $stmt = $pdo->prepare("SELECT * FROM students WHERE roll_number = ?");
    $stmt->execute([$roll]);
    $student = $stmt->fetch();

    if (!$student) {
        $error = "No student found!";
    } else {
        $student_info = "
            <h2>STUDENT RESULT DASHBOARD</h2>
            <p><strong>Name:</strong> {$student['name']}</p>
            <p><strong>Class:</strong> {$student['class']}</p>
        ";

        $stmt2 = $pdo->prepare("SELECT * FROM results WHERE student_id = ?");
        $stmt2->execute([$student["id"]]);
        $rows = $stmt2->fetchAll();

        if ($rows) {
            $results_table = "
                <table>
                    <tr>
                        <th>Subject</th>
                        <th>Marks</th>
                        <th>Out Of</th>
                        <th>Percentage</th>
                    </tr>
            ";
            foreach ($rows as $r) {
                $perc = round(($r["marks"] / $r["out_of"]) * 100) . "%";
                $results_table .= "
                    <tr>
                        <td>{$r['subject']}</td>
                        <td>{$r['marks']}</td>
                        <td>{$r['out_of']}</td>
                        <td>$perc</td>
                    </tr>";
            }
            $results_table .= "</table>";
        } else {
            $results_table = "<p>No result found.</p>";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Dashboard</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="dashboard-box">
    <a href="logout.php" class="logout">Logout</a>

    <h2>Search Student Result</h2>

    <form method="post">
        <input type="text" name="roll" placeholder="Enter Roll Number" required>
        <button type="submit" name="search">Search</button>
    </form>

    <?php
    if ($error) echo "<p class='error'>$error</p>";
    echo $student_info;
    echo $results_table;
    ?>
</div>

</body>
</html>
