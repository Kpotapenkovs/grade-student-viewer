<?php
require_once 'config.php';
include 'role_navbar.php';
include 'navbar.php';


try {
    $stmt = $pdo->query("
        SELECT 
            students.first_name, 
            students.last_name, 
            subject.subject_name, 
            grades.grade, 
            grades.date
        FROM grades
        JOIN students ON grades.student_id = students.id
        JOIN subject ON grades.subject_id = subject.id
        ORDER BY students.last_name, students.first_name, subject.subject_name
    ");
    $results = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Kļūda: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Studentu vērtējumi</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Studentu vērtējumu saraksts</h2>
    <table>
        <tr>
            <th>Vārds</th>
            <th>Uzvārds</th>
            <th>Priekšmets</th>
            <th>Atzīme</th>
            <th>Datums</th>
        </tr>
        <?php if (count($results) > 0): ?>
            <?php foreach ($results as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['first_name']) ?></td>
                    <td><?= htmlspecialchars($row['last_name']) ?></td>
                    <td><?= htmlspecialchars($row['subject_name']) ?></td>
                    <td><?= htmlspecialchars($row['grade']) ?></td>
                    <td><?= htmlspecialchars($row['date']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="5">Nav datu.</td></tr>
        <?php endif; ?>
    </table>
</body>
</html>
