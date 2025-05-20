<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: student_login.php");
    exit;
}

$student_id = $_SESSION['student_id'];
$filter_subject = $_GET['subject'] ?? '';

// Ielasa visus priekšmetus dropdownam
$subjects = $pdo->query("SELECT id, subject_name FROM subject")->fetchAll();

// Sagatavo vaicājumu ar filtru
if ($filter_subject) {
    $stmt = $pdo->prepare("
        SELECT sub.subject_name, g.grade, g.date
        FROM grades g
        JOIN subject sub ON g.subject_id = sub.id
        WHERE g.student_id = ? AND sub.id = ?
        ORDER BY g.date DESC
    ");
    $stmt->execute([$student_id, $filter_subject]);
} else {
    $stmt = $pdo->prepare("
        SELECT sub.subject_name, g.grade, g.date
        FROM grades g
        JOIN subject sub ON g.subject_id = sub.id
        WHERE g.student_id = ?
        ORDER BY g.date DESC
    ");
    $stmt->execute([$student_id]);
}
$grades = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Manas atzīmes</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'role_navbar.php'; ?>

<h2>Manas atzīmes</h2>

<form method="GET" action="">
    <label>Priekšmets:</label>
    <select name="subject">
        <option value="">-- Visi priekšmeti --</option>
        <?php foreach ($subjects as $sub): ?>
            <option value="<?= $sub['id'] ?>" <?= $filter_subject == $sub['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($sub['subject_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit">Filtrēt</button>
</form>

<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>Priekšmets</th>
        <th>Atzīme</th>
        <th>Datums</th>
    </tr>
    <?php foreach ($grades as $g): ?>
        <tr>
            <td><?= htmlspecialchars($g['subject_name']) ?></td>
            <td><?= htmlspecialchars($g['grade']) ?></td>
            <td><?= htmlspecialchars($g['date']) ?></td>
        </tr>
    <?php endforeach; ?>
    <?php if (empty($grades)): ?>
        <tr><td colspan="3">Nav atzīmju vai priekšmets netika atrasts.</td></tr>
    <?php endif; ?>
</table>

</body>
</html>
