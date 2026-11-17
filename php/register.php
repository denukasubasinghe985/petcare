<?php

require_once 'db.php';

require_post();

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

if (
    empty($name)
    || strlen($name) > 100
    || !filter_var($email, FILTER_VALIDATE_EMAIL)
    || strlen($email) > 190
    || strlen($password) < 8
    || $password !== $confirm
) {

    respond(false, 'Please enter valid registration details', [], 400);
}

$hashedPassword = password_hash(
    $password,
    PASSWORD_DEFAULT
);

$sql = "INSERT INTO users (name, email, password)
        VALUES (?, ?, ?)";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "sss",
    $name,
    $email,
    $hashedPassword
);

try {

    $stmt->execute();

    respond(true, 'Registration successful');

} catch (mysqli_sql_exception $e) {

    if ($e->getCode() === 1062) {

        respond(false, 'Email already registered', [], 409);
    }

    respond(false, 'Registration failed', [], 500);
}