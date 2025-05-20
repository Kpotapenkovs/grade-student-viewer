<?php
require_once 'config.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_name = trim($_POST['subject_name']);

    if (empty($subject_name)) {
        $errors[] = "Lūdzu, ievadiet priekšmeta nosaukumu.";
    } else {
        // Pārbauda vai priekšmets jau eksistē
        $stmt = $pdo->prepare("SELECT id FROM subject WHERE subject_name = ?");
        $stmt->execute([$subject_name]);
        if ($stmt->fetch()) {
            $errors[] = "Šāds priekšmets jau eksistē.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO subject (subject_name) VALUES (?)");
            $stmt->execute([$subject_name]);
            $success = "Priekšmets veiksmīgi pievienots!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Pievienot priekšmetu</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<h2>Pievienot jaunu priekšmetu</h2>

<?php foreach ($errors as $e): ?>
    <p style="color: red;"><?= htmlspecialchars($e) ?></p>
<?php endforeach; ?>

<?php if ($success): ?>
    <p style="color: green;"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<form method="POST" action="">
    <label>Priekšmeta nosaukums:</label>
    <input type="text" name="subject_name" required>
    <button type="submit">Pievienot</button>
</form>

<p><a href="edit_student.php">⬅ Atpakaļ</a></p>

</body>
</html>
