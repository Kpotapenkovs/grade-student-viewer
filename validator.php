<?php

function validateName($name) {
    return strlen(trim($name)) >= 2 && preg_match("/^[a-zA-Zā-žĀ-Ž\s'-]+$/u", $name);
}

function validateGrade($grade) {
    return is_numeric($grade) && $grade >= 0 && $grade <= 10;
}

function validateStudentInput($first, $last, $subject, $grade, &$errors) {
    $valid = true;

    if (!validateName($first)) {
        $errors[] = "Nederīgs vārds (jābūt vismaz 2 burtiem, tikai burti un atstarpes).";
        $valid = false;
    }

    if (!validateName($last)) {
        $errors[] = "Nederīgs uzvārds.";
        $valid = false;
    }

    if (!validateGrade($grade)) {
        $errors[] = "Atzīmei jābūt skaitlim no 0 līdz 10.";
        $valid = false;
    }

    return $valid;
}
