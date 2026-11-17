<?php

require_once 'db.php';

require_post();

$user_id = require_login();

$pet_name = trim($_POST['pet_name'] ?? '');
$service = $_POST['service'] ?? '';
$date = $_POST['date'] ?? '';

$status = "Pending";

if (!valid_appointment($pet_name, $service, $date)) {

    respond(false, 'Invalid appointment details', [], 400);
}

$sql = "INSERT INTO appointments
        (user_id, pet_name, service, date, status)
        VALUES (?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "issss",
    $user_id,
    $pet_name,
    $service,
    $date,
    $status
);

$stmt->execute();

respond(true, 'Appointment booked successfully');