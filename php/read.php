<?php

require_once 'db.php';

$user_id = require_login();

if (isset($_GET['id'])) {

    $id = (int) $_GET['id'];

    $sql = "SELECT * FROM appointments
            WHERE id = ? AND user_id = ?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("ii", $id, $user_id);

    $stmt->execute();

    $appointment = $stmt->get_result()->fetch_assoc();

    if (!$appointment) {

        respond(false, 'Appointment not found', [], 404);
    }

    respond(true, 'Appointment loaded', [
        'appointment' => $appointment
    ]);
}

$sql = "SELECT * FROM appointments
        WHERE user_id = ?
        ORDER BY date ASC";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $user_id);

$stmt->execute();

$result = $stmt->get_result();

$appointments = $result->fetch_all(MYSQLI_ASSOC);

respond(true, 'Appointments loaded', [
    'appointments' => $appointments
]);