<?php

require_once 'db.php';

require_post();

$user_id = require_login();

$id = (int) ($_POST['id'] ?? 0);

$pet_name = trim($_POST['pet_name'] ?? '');
$service = $_POST['service'] ?? '';
$date = $_POST['date'] ?? '';

if (
    $id <= 0
    || !valid_appointment($pet_name, $service, $date)
) {

    respond(false, 'Invalid appointment details', [], 400);
}

$check = $conn->prepare(
    "SELECT id FROM appointments
     WHERE id = ? AND user_id = ?"
);

$check->bind_param("ii", $id, $user_id);

$check->execute();

if (!$check->get_result()->fetch_assoc()) {

    respond(false, 'Appointment not found', [], 404);
}

$sql = "UPDATE appointments
        SET pet_name = ?, service = ?, date = ?
        WHERE id = ? AND user_id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "sssii",
    $pet_name,
    $service,
    $date,
    $id,
    $user_id
);

$stmt->execute();

respond(true, 'Appointment updated successfully');