<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $subject_name = trim($_POST['subject_name']);
    $grade = trim($_POST['grade']);

    try {
        $stmt = $pdo->prepare("SELECT id FROM students WHERE first_name = ? AND last_name = ?");
        $stmt->execute([$first_name, $last_name]);
        $student = $stmt->fetch();

        if ($student) {
            $student_id = $student['id'];
        } else {
            $stmt = $pdo->prepare("INSERT INTO students (first_name, last_name) VALUES (?, ?)");
            $stmt->execute([$first_name, $last_name]);
            $student_id = $pdo->lastInsertId();
        }

        $stmt = $pdo->prepare("SELECT id FROM subject WHERE subject_name = ?");
        $stmt->execute([$subject_name]);
        $subject = $stmt->fetch();

        if ($subject) {
            $subject_id = $subject['id'];
        } else {
            $stmt = $pdo->prepare("INSERT INTO subject (subject_name) VALUES (?)");
            $stmt->execute([$subject_name]);
            $subject_id = $pdo->lastInsertId();
        }

        $stmt = $pdo->prepare("INSERT INTO grades (student_id, subject_id, grade) VALUES (?, ?, ?)");
        $stmt->execute([$student_id, $subject_id, $grade]);

        $success_message = "Vērtējums veiksmīgi pievienots studentam {$first_name} {$last_name}!";
    } catch (PDOException $e) {
        $error_message = "Kļūda: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Pievienot studentu un atzīmi</title>
</head>
<body>
    <h2>Pievienot studentu un atzīmi</h2>

    <?php if (!empty($success_message)) echo "<p style='color:green;'>$success_message</p>"; ?>
    <?php if (!empty($error_message)) echo "<p style='color:red;'>$error_message</p>"; ?>

    <form method="POST" action="">
        <label for="first_name">Vārds:</label><br>
        <input type="text" id="first_name" name="first_name" required><br><br>

        <label for="last_name">Uzvārds:</label><br>
        <input type="text" id="last_name" name="last_name" required><br><br>

        <label for="subject_name">Priekšmets:</label><br>
        <input type="text" id="subject_name" name="subject_name" required><br><br>

        <label for="grade">Atzīme:</label><br>
        <input type="number" step="0.01" id="grade" name="grade" required><br><br>

        <button type="submit">Pievienot</button>
    </form>
</body>
</html>
