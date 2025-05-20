<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first = $_POST['first_name'];
    $last = $_POST['last_name'];

    $stmt = $pdo->prepare("SELECT * FROM students WHERE first_name = ? AND last_name = ?");
    $stmt->execute([$first, $last]);
    $student = $stmt->fetch();

    if ($student) {
        $_SESSION['student_id'] = $student['id'];
        header("Location: student_view.php");
        exit;
    } else {
        $error = "Students netika atrasts.";
    }
}
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Studentu Pieslēgšanās</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'role_navbar.php'; ?>

<h2>Pieslēgties kā students</h2>
<?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
<form method="POST" action="">
    <input type="text" name="first_name" placeholder="Vārds" required>
    <input type="text" name="last_name" placeholder="Uzvārds" required>
    <button type="submit">Ienākt</button>
</form>

</body>
</html>
