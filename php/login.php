<?php

require_once 'db.php';

require_post();

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

$sql = "SELECT id, name, password
        FROM users
        WHERE email = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("s", $email);

$stmt->execute();

$result = $stmt->get_result();

$user = $result->fetch_assoc();

if ($user && password_verify(
    $password,
    $user['password']
)) {

    session_regenerate_id(true);

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['name'] = $user['name'];

    unset($_SESSION['csrf']);

    respond(true, 'Login successful');

} else {

    respond(false, 'Invalid email or password', [], 401);
}