<?php

$host = 'localhost';     
$dbname = 'student_grade_viewer'; 
$username = 'root';      
$password = '';          


try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {

    echo "Savienojuma kļūda: " . $e->getMessage();
    exit;
}
?>