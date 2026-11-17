<?php

session_start();

date_default_timezone_set('Asia/Colombo');

header('Content-Type: application/json');
header('Cache-Control: no-store');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function respond($success, $message, $extra = [], $code = 200)
{
    http_response_code($code);

    echo json_encode(array_merge([
        'success' => $success,
        'message' => $message
    ], $extra));

    exit;
}

try {

$conn = new mysqli(
    "127.0.0.1",
    "root",
    "",
    "petcare_db",
    3307
);
    $conn->set_charset("utf8mb4");

} catch (mysqli_sql_exception $e) {

    respond(false, $e->getMessage(), [], 500);

}
function csrf_token()
{
    if (!isset($_SESSION['csrf'])) {

        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf'];
}

function require_post()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

        respond(false, 'Invalid request method', [], 405);
    }

    if (!hash_equals(csrf_token(), $_POST['csrf'] ?? '')) {

        respond(false, 'Invalid security token', [], 403);
    }
}

function require_login()
{
    if (!isset($_SESSION['user_id'])) {

        respond(false, 'Please log in first', [], 401);
    }

    return $_SESSION['user_id'];
}

function valid_appointment($pet, $service, $date)
{
    $services = [
        'Veterinary Checkup',
        'Pet Grooming',
        'Vaccination',
        'Pet Boarding'
    ];

    $d = DateTime::createFromFormat('!Y-m-d', $date);

    return $pet !== ''
        && strlen($pet) <= 100
        && in_array($service, $services, true)
        && $d
        && $d->format('Y-m-d') === $date
        && $date >= date('Y-m-d');
}