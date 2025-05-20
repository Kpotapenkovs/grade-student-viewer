<?php
require_once 'config.php';
require_once 'validator.php';

$errors = [];

// Dzēšana
if (isset($_GET['delete_id'])) {
    $deleteId = $_GET['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM grades WHERE id = ?");
    $stmt->execute([$deleteId]);
    header("Location: edit_student.php");
    exit;
}

// Rediģēšana
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_grade'])) {
    $grade_id = $_POST['grade_id'];
    $grade = $_POST['grade'];
    $subject_id = $_POST['subject_id'];

    if (validateGrade($grade)) {
        $stmt = $pdo->prepare("UPDATE grades SET grade = ?, subject_id = ? WHERE id = ?");
        $stmt->execute([$grade, $subject_id, $grade_id]);
    } else {
        $errors[] = "Nederīga atzīme.";
    }
}

// Jauna studenta pievienošana
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_student'])) {
    $first = trim($_POST['first_name']);
    $last = trim($_POST['last_name']);
    $subject_id = $_POST['subject_id'];
    $grade = $_POST['grade'];

    if (validateStudentInput($first, $last, '', $grade, $errors)) {
        $stmt = $pdo->prepare("SELECT id FROM students WHERE first_name = ? AND last_name = ?");
        $stmt->execute([$first, $last]);
        $student = $stmt->fetch();

        if ($student) {
            $student_id = $student['id'];
        } else {
            $stmt = $pdo->prepare("INSERT INTO students (first_name, last_name) VALUES (?, ?)");
            $stmt->execute([$first, $last]);
            $student_id = $pdo->lastInsertId();
        }

        $stmt = $pdo->prepare("INSERT INTO grades (student_id, subject_id, grade, date) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$student_id, $subject_id, $grade]);
        header("Location: edit_student.php");
        exit;
    }
}

// Filtrācija
$subjects = $pdo->query("SELECT id, subject_name FROM subject")->fetchAll();

$first_name_filter = $_GET['first_name'] ?? '';
$last_name_filter = $_GET['last_name'] ?? '';
$subject_filter = $_GET['subject'] ?? '';
$order = $_GET['order'] ?? 'asc';

$query = "
    SELECT g.id, s.first_name, s.last_name, sub.subject_name, sub.id AS subject_id, g.grade, g.date
    FROM grades g
    JOIN students s ON g.student_id = s.id
    JOIN subject sub ON g.subject_id = sub.id
    WHERE 1=1
";

$params = [];

if ($first_name_filter) {
    $query .= " AND s.first_name LIKE ?";
    $params[] = "%$first_name_filter%";
}

if ($last_name_filter) {
    $query .= " AND s.last_name LIKE ?";
    $params[] = "%$last_name_filter%";
}

if ($subject_filter) {
    $query .= " AND sub.id = ?";
    $params[] = $subject_filter;
}

$query .= " ORDER BY s.last_name " . ($order === 'desc' ? 'DESC' : 'ASC') . ", s.first_name " . ($order === 'desc' ? 'DESC' : 'ASC');

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$grades = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Rediģēt studentus</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php include 'role_navbar.php'; include 'navbar.php'; ?>

<h2>Rediģēt studentus un atzīmes</h2>

<!-- Filtrācijas forma -->
<form method="GET" class="filter-form">
    <label>Vārds:</label>
    <input type="text" name="first_name" value="<?= htmlspecialchars($first_name_filter) ?>">

    <label>Uzvārds:</label>
    <input type="text" name="last_name" value="<?= htmlspecialchars($last_name_filter) ?>">

    <label>Priekšmets:</label>
    <select name="subject">
        <option value="">-- Visi --</option>
        <?php foreach ($subjects as $s): ?>
            <option value="<?= $s['id'] ?>" <?= $subject_filter == $s['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($s['subject_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Sakārtot:</label>
    <select name="order">
        <option value="asc" <?= $order === 'asc' ? 'selected' : '' ?>>A-Z</option>
        <option value="desc" <?= $order === 'desc' ? 'selected' : '' ?>>Z-A</option>
    </select>

    <button type="submit">Filtrēt</button>
    <a href="edit_student.php"><button type="button">Notīrīt</button></a>
</form>

<?php foreach ($errors as $e): ?>
    <p style="color: red;"><?= htmlspecialchars($e) ?></p>
<?php endforeach; ?>

<!-- Tabula -->
<table>
    <thead>
        <tr>
            <th>Vārds</th>
            <th>Uzvārds</th>
            <th>Priekšmets</th>
            <th>Atzīme</th>
            <th>Datums</th>
            <th>Darbības</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($grades as $g): ?>
        <tr>
            <form method="POST">
                <td><?= htmlspecialchars($g['first_name']) ?></td>
                <td><?= htmlspecialchars($g['last_name']) ?></td>
                <td>
                    <select name="subject_id">
                        <?php foreach ($subjects as $s): ?>
                            <option value="<?= $s['id'] ?>" <?= $s['id'] == $g['subject_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($s['subject_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td>
                    <input type="number" name="grade" value="<?= $g['grade'] ?>" min="0" max="10" step="0.1">
                </td>
                <td><?= $g['date'] ?></td>
                <td>
                    <input type="hidden" name="grade_id" value="<?= $g['id'] ?>">
                    <button type="submit" name="update_grade">Saglabāt</button>
                    <a href="?delete_id=<?= $g['id'] ?>" onclick="return confirm('Vai tiešām dzēst šo ierakstu?')">Dzēst</a>
                </td>
            </form>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<h3>Pievienot jaunu studentu un atzīmi</h3>
<form method="POST">
    <label>Vārds:</label>
    <input type="text" name="first_name" required>
    <label>Uzvārds:</label>
    <input type="text" name="last_name" required>
    <label>Priekšmets:</label>
    <select name="subject_id" required>
        <?php foreach ($subjects as $s): ?>
            <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['subject_name']) ?></option>
        <?php endforeach; ?>
    </select>
    <label>Atzīme:</label>
    <input type="number" name="grade" min="0" max="10" step="0.1" required>
    <button type="submit" name="create_student">Pievienot</button>
</form>

</body>
</html>
